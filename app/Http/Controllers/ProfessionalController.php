<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\NotifiesSafely;
use App\Models\User;
use App\Notifications\ArchitectApproved;
use App\Notifications\SubscriptionActivated;
use App\Support\ArchitectSpecialties;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfessionalController extends Controller
{
    use NotifiesSafely;

    public function index(): View
    {
        $professionals = User::role('architect')->orderBy('name')->get();

        $stats = (object) [
            'total_professionals' => $professionals->count(),
            'active_professionals' => $professionals->filter(fn (User $u) => $u->approved_at && $u->subscription_active && ! $u->is_suspended)->count(),
            'inactive_professionals' => $professionals->filter(fn (User $u) => ! $u->approved_at || ! $u->subscription_active || $u->is_suspended)->count(),
        ];

        return view('professionals.index', [
            'professionals' => $professionals,
            'stats' => $stats,
            'specialties' => ArchitectSpecialties::OPTIONS,
            'firms' => $this->existingFirms(),
        ]);
    }

    public function show(int $id): View
    {
        $professional = User::role('architect')->findOrFail($id);

        return view('professionals.show', [
            'professional' => $professional,
            'specialties' => ArchitectSpecialties::OPTIONS,
            'firms' => $this->existingFirms(),
        ]);
    }

    public function approve(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $professional = User::role('architect')->findOrFail($id);

        $professional->forceFill([
            'approved_at' => now(),
            'registration_no' => $professional->registration_no ?? $this->nextRegistrationNo(),
        ])->save();

        $this->notifySafely($professional, new ArchitectApproved);

        $message = $professional->name.' has been approved as a registered architect.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'professional' => $this->transform($professional),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Approve the architect's Blue Book (ACZ Conditions of Engagement &
     * Scale of Fees) purchase and grant access. The architect must have
     * already declared their purchase via the portal — admin can't
     * proactively grant this without that request existing first.
     */
    public function approveBlueBook(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $professional = User::role('architect')->findOrFail($id);

        if (! $professional->blue_book_requested_at) {
            $message = $professional->name.' has not requested Blue Book access yet.';

            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $validated = $request->validate([
            'duration_years' => ['nullable', 'integer', 'min:1', 'max:10'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $expiresAt = isset($validated['expires_at'])
            ? $validated['expires_at']
            : now()->addYears($validated['duration_years'] ?? 1);

        $professional->forceFill([
            'subscription_active' => true,
            'subscription_expires_at' => $expiresAt,
        ])->save();

        $this->notifySafely($professional, new SubscriptionActivated($professional->subscription_expires_at?->format('Y-m-d')));

        $message = $professional->name."'s Blue Book access has been approved until ".$professional->subscription_expires_at->format('d M Y').'.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'professional' => $this->transform($professional),
            ]);
        }

        return back()->with('success', $message);
    }

    public function updateDetails(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $professional = User::role('architect')->findOrFail($id);

        $validated = $request->validate([
            'specialty' => ['nullable', Rule::in(ArchitectSpecialties::OPTIONS)],
            'firm_name' => ['nullable', 'string', 'max:255'],
        ]);

        $professional->update($validated);

        $message = $professional->name.'\'s professional details have been updated.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'professional' => array_merge($this->transform($professional), [
                    'registration_no' => $professional->registration_no,
                    'specialty' => $professional->specialty,
                    'firm_name' => $professional->firm_name,
                ]),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Registration numbers are assigned by the system the moment an
     * architect is approved — never entered by hand.
     */
    private function nextRegistrationNo(): string
    {
        $max = User::whereNotNull('registration_no')
            ->where('registration_no', 'like', 'ARCH-%')
            ->pluck('registration_no')
            ->map(fn (string $no) => (int) str_replace('ARCH-', '', $no))
            ->max() ?? 0;

        return 'ARCH-'.str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    private function existingFirms()
    {
        return User::role('architect')
            ->whereNotNull('firm_name')
            ->distinct()
            ->orderBy('firm_name')
            ->pluck('firm_name');
    }

    private function transform(User $professional): array
    {
        return [
            'id' => $professional->id,
            'is_suspended' => (bool) $professional->is_suspended,
            'approved_at' => optional($professional->approved_at)->format('Y-m-d'),
            'blue_book_requested_at' => optional($professional->blue_book_requested_at)->format('Y-m-d'),
            'subscription_active' => (bool) $professional->subscription_active,
            'subscription_expires_at' => optional($professional->subscription_expires_at)->format('d M Y'),
            'registration_no' => $professional->registration_no,
        ];
    }
}
