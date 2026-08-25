<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\NotifiesSafely;
use App\Http\Controllers\Controller;
use App\Models\Engagement;
use App\Models\User;
use App\Notifications\EngagementApproved;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EngagementController extends Controller
{
    use NotifiesSafely;

    public function index(Request $request)
    {
        $user = $request->user();

        $engagements = Engagement::where('client_id', $user->id)
            ->orWhere('architect_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $engagements->map(fn (Engagement $e) => $this->transform($e)),
        ]);
    }

    public function show(Request $request, int $id)
    {
        $engagement = Engagement::findOrFail($id);

        $this->authorize('view', $engagement);

        return response()->json($this->transform($engagement));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $this->authorize('create', Engagement::class);

        $validated = $request->validate([
            'architect_id' => ['required', 'integer'],
        ]);

        $architect = User::role('architect')
            ->whereNotNull('approved_at')
            ->where('subscription_active', true)
            ->where('is_suspended', false)
            ->find($validated['architect_id']);

        if (! $architect) {
            throw ValidationException::withMessages([
                'architect_id' => ['That architect is not currently active.'],
            ]);
        }

        $engagement = Engagement::create([
            'client_id' => $user->id,
            'architect_id' => $architect->id,
            'status' => Engagement::STATUS_PENDING_ARCHITECT_APPROVAL,
        ]);

        return response()->json($this->transform($engagement), 201);
    }

    public function approve(Request $request, int $id)
    {
        $engagement = Engagement::findOrFail($id);
        $user = $request->user();

        $this->authorize('approve', $engagement);

        if ($engagement->status !== Engagement::STATUS_PENDING_ARCHITECT_APPROVAL) {
            return response()->json(['message' => 'This engagement is not awaiting architect approval.'], 422);
        }

        $engagement->update([
            'status' => Engagement::STATUS_ARCHITECT_APPROVED,
            'architect_approved_at' => now(),
        ]);

        $this->notifySafely($engagement->client, new EngagementApproved($engagement));

        return response()->json($this->transform($engagement));
    }

    public function signContract(Request $request, int $id)
    {
        $engagement = Engagement::findOrFail($id);
        $user = $request->user();

        $this->authorize('signContract', $engagement);

        if (! in_array($engagement->status, [Engagement::STATUS_ARCHITECT_APPROVED, Engagement::STATUS_CONTRACT_SIGNED], true)) {
            return response()->json(['message' => 'The architect must approve this engagement before the contract can be signed.'], 422);
        }

        $update = [];

        if ($engagement->client_id === $user->id) {
            $update['client_signed_at'] = $engagement->client_signed_at ?? now();
        }

        if ($engagement->architect_id === $user->id) {
            $update['architect_signed_at'] = $engagement->architect_signed_at ?? now();
        }

        $engagement->fill($update);

        if ($engagement->client_signed_at && $engagement->architect_signed_at) {
            $engagement->status = Engagement::STATUS_CONTRACT_SIGNED;
        }

        $engagement->save();

        return response()->json($this->transform($engagement));
    }

    private function transform(Engagement $engagement): array
    {
        return [
            'id' => $engagement->id,
            'client_id' => $engagement->client_id,
            'architect_id' => $engagement->architect_id,
            'status' => $engagement->status,
            'architect_approved_at' => $engagement->architect_approved_at,
            'client_signed_at' => $engagement->client_signed_at,
            'architect_signed_at' => $engagement->architect_signed_at,
        ];
    }
}
