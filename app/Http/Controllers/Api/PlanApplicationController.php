<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\NotifiesSafely;
use App\Http\Controllers\Controller;
use App\Models\Engagement;
use App\Models\PlanApplication;
use App\Notifications\PlanApplicationDecided;
use App\Rules\FileIsVirusFree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PlanApplicationController extends Controller
{
    use NotifiesSafely;

    public function index(Request $request)
    {
        $user = $request->user();

        $query = PlanApplication::query()->latest();

        if ($user->hasRole('council')) {
            // council sees everything except drafts, which aren't submitted yet
            $query->where('status', '!=', PlanApplication::STATUS_DRAFT);
        } elseif ($user->hasRole('architect')) {
            $query->where('submitted_by', $user->id);
        } elseif ($user->hasRole('client')) {
            $query->whereHas('engagement', fn ($q) => $q->where('client_id', $user->id))
                ->where('status', '!=', PlanApplication::STATUS_DRAFT);
        } else {
            $query->whereRaw('1 = 0');
        }

        return response()->json([
            'data' => $query->get()->map(fn (PlanApplication $p) => $this->transform($p)),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $planApplication = PlanApplication::with([
            'comments.user',
            'markups.user',
            'drawings.uploadedBy',
            'submittedBy',
            'engagement.architect',
            'engagement.client',
        ])->findOrFail($id);

        $this->authorize('view', $planApplication);

        // A council reviewer opening a freshly-submitted application marks
        // it "under review" — so everyone can see it's been picked up,
        // not just sitting untouched in the queue.
        if ($request->user()->hasRole('council') && $planApplication->status === PlanApplication::STATUS_PENDING) {
            $planApplication->update(['status' => PlanApplication::STATUS_UNDER_REVIEW]);
        }

        return response()->json($this->transform($planApplication, includeComments: true, includeParties: true));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $this->authorize('create', PlanApplication::class);

        $validated = $request->validate(array_merge(
            ['engagement_id' => ['required', 'integer']],
            $this->fieldRules()
        ));

        $engagement = Engagement::where('id', $validated['engagement_id'])
            ->where('architect_id', $user->id)
            ->where('status', Engagement::STATUS_CONTRACT_SIGNED)
            ->first();

        if (! $engagement) {
            throw ValidationException::withMessages([
                'engagement_id' => ['This engagement is not eligible for a plan submission (contract must be signed).'],
            ]);
        }

        unset($validated['drawings']);

        $planApplication = PlanApplication::create(array_merge($validated, [
            'submitted_by' => $user->id,
            'status' => PlanApplication::STATUS_PENDING,
        ]));

        $this->storeDrawingsIfPresent($request, $planApplication);

        return response()->json($this->transform($planApplication), 201);
    }

    /**
     * Save-and-continue: upsert a draft with whatever fields have been
     * completed so far. Called after every wizard step so progress is
     * durable on the backend, not just the portal's PHP session.
     */
    public function saveDraft(Request $request)
    {
        $user = $request->user();

        if (! $user->hasRole('architect')) {
            return response()->json(['message' => 'Only architects can save a plan application draft.'], 403);
        }

        $engagementId = $request->integer('engagement_id');

        $existingDraft = PlanApplication::where('engagement_id', $engagementId)
            ->where('submitted_by', $user->id)
            ->where('status', PlanApplication::STATUS_DRAFT)
            ->first();

        $rules = array_merge(['engagement_id' => ['required', 'integer']], $this->looseFieldRules());
        $rules['plan_no'] = ['nullable', 'string', Rule::unique('plan_applications', 'plan_no')->ignore($existingDraft?->id)];

        $validated = $request->validate($rules);

        $engagement = Engagement::where('id', $validated['engagement_id'])
            ->where('architect_id', $user->id)
            ->where('status', Engagement::STATUS_CONTRACT_SIGNED)
            ->first();

        if (! $engagement) {
            throw ValidationException::withMessages([
                'engagement_id' => ['This engagement is not eligible for a plan submission (contract must be signed).'],
            ]);
        }

        $fields = collect($validated)->except('engagement_id')->toArray();

        $draft = $existingDraft;

        if ($draft) {
            $draft->update($fields);
        } else {
            $draft = PlanApplication::create(array_merge($fields, [
                'engagement_id' => $engagement->id,
                'submitted_by' => $user->id,
                'status' => PlanApplication::STATUS_DRAFT,
            ]));
        }

        return response()->json($this->transform($draft));
    }

    /**
     * Fetch the architect's in-progress draft for an engagement, if any —
     * lets the wizard resume exactly where they left off, even from a
     * different device or after logging back in.
     */
    public function getDraft(Request $request, int $engagementId)
    {
        $draft = PlanApplication::where('engagement_id', $engagementId)
            ->where('submitted_by', $request->user()->id)
            ->where('status', PlanApplication::STATUS_DRAFT)
            ->first();

        return response()->json(['draft' => $draft ? $this->transform($draft) : null]);
    }

    /**
     * Finalize a draft: full validation against the merged draft + any
     * fields sent with this request, then transition draft -> pending.
     */
    public function finalize(Request $request, int $id)
    {
        $planApplication = PlanApplication::findOrFail($id);
        $user = $request->user();

        if ($planApplication->submitted_by !== $user->id) {
            return response()->json(['message' => 'Only the submitting architect can finalize this application.'], 403);
        }

        if ($planApplication->status !== PlanApplication::STATUS_DRAFT) {
            return response()->json(['message' => 'This application is not a draft.'], 422);
        }

        $rules = $this->fieldRules();
        unset($rules['drawings']);
        $rules['plan_no'] = ['required', 'string', Rule::unique('plan_applications', 'plan_no')->ignore($planApplication->id)];

        $merged = array_merge(
            $planApplication->only(array_keys($rules)),
            $request->only(array_keys($rules))
        );

        $validated = validator($merged, $rules)->validate();

        if ($request->hasFile('drawings')) {
            $request->validate(['drawings' => ['file', 'mimes:pdf,zip', 'max:20480', new FileIsVirusFree]]);
        } elseif (! $planApplication->drawings_path) {
            throw ValidationException::withMessages([
                'drawings' => ['Drawings must be uploaded before the application can be submitted.'],
            ]);
        }

        $planApplication->update(array_merge($validated, [
            'status' => PlanApplication::STATUS_PENDING,
        ]));

        $this->storeDrawingsIfPresent($request, $planApplication);

        return response()->json($this->transform($planApplication));
    }

    public function resubmit(Request $request, int $id)
    {
        $planApplication = PlanApplication::findOrFail($id);
        $user = $request->user();

        $this->authorize('resubmit', $planApplication);

        if ($planApplication->status !== PlanApplication::STATUS_REVISION_REQUESTED) {
            return response()->json(['message' => 'This application is not awaiting revision.'], 422);
        }

        $rules = $this->fieldRules();
        $rules['plan_no'] = ['required', 'string', Rule::unique('plan_applications', 'plan_no')->ignore($planApplication->id)];

        $validated = $request->validate($rules);
        unset($validated['drawings']);

        $planApplication->update(array_merge($validated, [
            'status' => PlanApplication::STATUS_PENDING,
        ]));

        $this->storeDrawingsIfPresent($request, $planApplication);

        return response()->json($this->transform($planApplication));
    }

    public function downloadDrawings(Request $request, int $id)
    {
        $planApplication = PlanApplication::findOrFail($id);

        $this->authorize('view', $planApplication);

        if (! $planApplication->drawings_path || ! Storage::disk('local')->exists($planApplication->drawings_path)) {
            return response()->json(['message' => 'No drawings have been uploaded for this application.'], 404);
        }

        if ($request->boolean('preview')) {
            return Storage::disk('local')->response($planApplication->drawings_path, $planApplication->drawings_original_name, [
                'Content-Disposition' => 'inline; filename="'.$planApplication->drawings_original_name.'"',
            ]);
        }

        return Storage::disk('local')->download($planApplication->drawings_path, $planApplication->drawings_original_name);
    }

    /**
     * Download a specific historical version of the drawings — every
     * upload is kept, never overwritten, so earlier submissions stay
     * available as a record even after the architect corrects and
     * re-uploads.
     */
    public function downloadDrawingVersion(Request $request, int $id, int $version)
    {
        $planApplication = PlanApplication::findOrFail($id);

        $this->authorize('view', $planApplication);

        $drawing = $planApplication->drawings()->where('version', $version)->first();

        if (! $drawing || ! Storage::disk('local')->exists($drawing->path)) {
            return response()->json(['message' => 'That drawing version was not found.'], 404);
        }

        if ($request->boolean('preview')) {
            return Storage::disk('local')->response($drawing->path, $drawing->original_name, [
                'Content-Disposition' => 'inline; filename="'.$drawing->original_name.'"',
            ]);
        }

        return Storage::disk('local')->download($drawing->path, $drawing->original_name);
    }

    /**
     * List the professional markups (pins/lines with review comments) that
     * council has placed on the *current* drawing version.
     */
    public function markups(Request $request, int $id)
    {
        $planApplication = PlanApplication::findOrFail($id);

        $this->authorize('view', $planApplication);

        return response()->json([
            'data' => $planApplication->currentMarkups()->with('user')->get()
                ->map(fn (\App\Models\PlanApplicationMarkup $m) => $this->transformMarkup($m)),
        ]);
    }

    /**
     * Council places a pin or line marker on the drawing, with a review
     * comment attached, so the architect can see exactly what needs fixing.
     */
    public function storeMarkup(Request $request, int $id)
    {
        $planApplication = PlanApplication::findOrFail($id);
        $user = $request->user();

        // Same authorization as adding a technical review comment: council only.
        $this->authorize('comment', $planApplication);

        $validated = $request->validate([
            'type' => ['required', Rule::in(['pin', 'line'])],
            'page' => ['nullable', 'integer', 'min:1'],
            'x' => ['required', 'numeric', 'between:0,1'],
            'y' => ['required', 'numeric', 'between:0,1'],
            'x2' => ['required_if:type,line', 'nullable', 'numeric', 'between:0,1'],
            'y2' => ['required_if:type,line', 'nullable', 'numeric', 'between:0,1'],
            'comment' => ['nullable', 'string'],
        ]);

        $markup = $planApplication->markups()->create(array_merge($validated, [
            'user_id' => $user->id,
            'page' => $validated['page'] ?? 1,
            'drawing_version' => $planApplication->drawings_version,
        ]));

        $markup->load('user');

        return response()->json($this->transformMarkup($markup), 201);
    }

    public function destroyMarkup(Request $request, int $id, int $markupId)
    {
        $planApplication = PlanApplication::findOrFail($id);
        $user = $request->user();

        $this->authorize('comment', $planApplication);

        $markup = $planApplication->markups()->where('id', $markupId)->firstOrFail();

        if ($markup->user_id !== $user->id) {
            return response()->json(['message' => 'You can only remove your own markups.'], 403);
        }

        $markup->delete();

        return response()->json(['message' => 'Markup removed.']);
    }

    public function storeComment(Request $request, int $id)
    {
        $planApplication = PlanApplication::findOrFail($id);
        $user = $request->user();

        $this->authorize('comment', $planApplication);

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $comment = $planApplication->comments()->create([
            'user_id' => $user->id,
            'body' => $validated['body'],
        ]);

        return response()->json([
            'id' => $comment->id,
            'body' => $comment->body,
            'user_id' => $comment->user_id,
            'created_at' => $comment->created_at,
        ], 201);
    }

    public function decide(Request $request, int $id)
    {
        $planApplication = PlanApplication::findOrFail($id);
        $user = $request->user();

        $this->authorize('decide', $planApplication);

        $validated = $request->validate([
            'decision' => ['required', Rule::in([
                PlanApplication::STATUS_APPROVED,
                PlanApplication::STATUS_REJECTED,
                PlanApplication::STATUS_REVISION_REQUESTED,
            ])],
            'comment' => ['nullable', 'string'],
        ]);

        $planApplication->update([
            'status' => $validated['decision'],
            'council_reviewer_id' => $user->id,
        ]);

        if (! empty($validated['comment'])) {
            $planApplication->comments()->create([
                'user_id' => $user->id,
                'body' => $validated['comment'],
            ]);
        }

        $this->notifySafely($planApplication->submittedBy, new PlanApplicationDecided($planApplication));

        return response()->json($this->transform($planApplication));
    }

    private function fieldRules(): array
    {
        return [
            'plan_no' => ['required', 'string', 'unique:plan_applications,plan_no'],
            'stand_no' => ['required', 'string'],
            'postal_address' => ['required', 'string'],
            'estimated_cost' => ['required', 'numeric'],
            'purpose' => ['required', 'string'],
            'industry_type' => ['nullable', 'string'],
            'project_type' => ['required', 'string'],
            'owner_name' => ['required', 'string'],
            'owner_address' => ['required', 'string'],
            'owner_phone' => ['nullable', 'string'],
            'architect_name' => ['nullable', 'string'],
            'architect_address' => ['nullable', 'string'],
            'architect_phone' => ['nullable', 'string'],
            'contractor_name' => ['nullable', 'string'],
            'contractor_address' => ['nullable', 'string'],
            'contractor_phone' => ['nullable', 'string'],
            'supervision' => ['required', 'string'],
            'area_ground_floor' => ['required', 'numeric'],
            'area_first_floor' => ['nullable', 'numeric'],
            'area_total' => ['required', 'numeric'],
            'area_outbuildings' => ['nullable', 'numeric'],
            'fire_fighting_equipment' => ['nullable', 'string'],
            'drawings' => ['nullable', 'file', 'mimes:pdf,zip', 'max:20480', new FileIsVirusFree],
        ];
    }

    /**
     * Same shape as fieldRules() but every field is optional — used when
     * saving a draft, where only the fields for the current wizard step
     * are present in the request.
     */
    private function looseFieldRules(): array
    {
        return collect($this->fieldRules())
            ->except('drawings')
            ->map(function (array $rules) {
                $rules = array_values(array_filter($rules, fn ($rule) => $rule !== 'required' && ! ($rule instanceof Rule)));

                return array_merge(['nullable'], $rules);
            })
            ->toArray();
    }

    /**
     * Store a newly-uploaded drawing as the *next* version — never deletes
     * or overwrites what was there before, so every submission stays on
     * record. `drawings_path`/`drawings_original_name`/`drawings_version`
     * on the application just point at whichever version is current, for
     * existing code that reads them directly.
     */
    private function storeDrawingsIfPresent(Request $request, PlanApplication $planApplication): void
    {
        if (! $request->hasFile('drawings')) {
            return;
        }

        $file = $request->file('drawings');
        $version = $planApplication->drawings_version + 1;
        $path = $file->store('plan-drawings', 'local');

        $planApplication->drawings()->create([
            'uploaded_by' => $request->user()->id,
            'version' => $version,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);

        $planApplication->update([
            'drawings_path' => $path,
            'drawings_original_name' => $file->getClientOriginalName(),
            'drawings_version' => $version,
        ]);
    }

    private function transformMarkup(\App\Models\PlanApplicationMarkup $m): array
    {
        return [
            'id' => $m->id,
            'type' => $m->type,
            'page' => $m->page,
            'x' => $m->x,
            'y' => $m->y,
            'x2' => $m->x2,
            'y2' => $m->y2,
            'comment' => $m->comment,
            'user_id' => $m->user_id,
            'user_name' => $m->user?->name,
            'created_at' => $m->created_at,
        ];
    }

    private function transformParty(?\App\Models\User $user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'firm_name' => $user->firm_name,
            'registration_no' => $user->registration_no,
        ];
    }

    private function transform(PlanApplication $p, bool $includeComments = false, bool $includeParties = false): array
    {
        $data = [
            'id' => $p->id,
            'engagement_id' => $p->engagement_id,
            'submitted_by' => $p->submitted_by,
            'council_reviewer_id' => $p->council_reviewer_id,
            'plan_no' => $p->plan_no,
            'stand_no' => $p->stand_no,
            'postal_address' => $p->postal_address,
            'estimated_cost' => $p->estimated_cost,
            'purpose' => $p->purpose,
            'industry_type' => $p->industry_type,
            'project_type' => $p->project_type,
            'owner_name' => $p->owner_name,
            'owner_address' => $p->owner_address,
            'owner_phone' => $p->owner_phone,
            'architect_name' => $p->architect_name,
            'architect_address' => $p->architect_address,
            'architect_phone' => $p->architect_phone,
            'contractor_name' => $p->contractor_name,
            'contractor_address' => $p->contractor_address,
            'contractor_phone' => $p->contractor_phone,
            'supervision' => $p->supervision,
            'area_ground_floor' => $p->area_ground_floor,
            'area_first_floor' => $p->area_first_floor,
            'area_total' => $p->area_total,
            'area_outbuildings' => $p->area_outbuildings,
            'fire_fighting_equipment' => $p->fire_fighting_equipment,
            'has_drawings' => (bool) $p->drawings_path,
            'drawings_version' => $p->drawings_version,
            'drawings_original_name' => $p->drawings_original_name,
            'status' => $p->status,
            'created_at' => $p->created_at,
        ];

        if ($includeComments) {
            $data['comments'] = $p->comments->map(fn ($c) => [
                'id' => $c->id,
                'user_id' => $c->user_id,
                'user_name' => $c->user?->name,
                'body' => $c->body,
                'created_at' => $c->created_at,
            ]);

            // Only markups placed on the *current* drawing — corrections
            // marked on a superseded version don't carry over onto a new one.
            $data['markups'] = $p->markups
                ->where('drawing_version', $p->drawings_version)
                ->values()
                ->map(fn (\App\Models\PlanApplicationMarkup $m) => $this->transformMarkup($m));

            $data['drawings_history'] = $p->drawings->map(fn (\App\Models\PlanApplicationDrawing $d) => [
                'version' => $d->version,
                'original_name' => $d->original_name,
                'uploaded_by_name' => $d->uploadedBy?->name,
                'created_at' => $d->created_at,
                'is_current' => $d->version === $p->drawings_version,
            ]);
        }

        if ($includeParties) {
            $data['architect'] = $this->transformParty($p->submittedBy ?? $p->engagement?->architect);
            $data['client'] = $this->transformParty($p->engagement?->client);
        }

        return $data;
    }
}
