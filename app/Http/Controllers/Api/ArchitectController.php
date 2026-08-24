<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArchitectController extends Controller
{
    public function index(Request $request)
    {
        $architects = $this->visibleArchitects()
            ->when($request->query('q'), function (Builder $query, string $q) {
                $query->where(function (Builder $inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('firm_name', 'like', "%{$q}%")
                        ->orWhere('specialty', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $architects->map(fn (User $architect) => $this->transform($architect)),
        ]);
    }

    public function show(int $id)
    {
        $architect = $this->visibleArchitects()->findOrFail($id);

        return response()->json($this->transform($architect));
    }

    /**
     * Architect declares they've purchased the ACZ Blue Book (Conditions of
     * Engagement & Scale of Fees) and is awaiting admin approval to be
     * granted access. This is a standing, architect-level credential — not
     * something purchased per engagement/project.
     */
    public function requestBlueBook(Request $request)
    {
        $user = $request->user();

        if (! $user->hasRole('architect')) {
            return response()->json(['message' => 'Only architects can request Blue Book access.'], 403);
        }

        if ($user->subscription_active) {
            return response()->json(['message' => 'You already have active Blue Book access.'], 422);
        }

        if ($user->blue_book_requested_at) {
            return response()->json(['message' => 'Your Blue Book purchase is already awaiting admin approval.'], 422);
        }

        $user->forceFill(['blue_book_requested_at' => now()])->save();

        return response()->json([
            'message' => 'Blue Book purchase recorded. Awaiting admin approval.',
            'blue_book_requested_at' => $user->blue_book_requested_at->format('Y-m-d H:i'),
        ]);
    }

    private function visibleArchitects(): Builder
    {
        return User::role('architect')
            ->whereNotNull('approved_at')
            ->where('subscription_active', true)
            ->where('is_suspended', false)
            ->whereNotNull('email_verified_at');
    }

    private function transform(User $architect): array
    {
        return [
            'id' => $architect->id,
            'name' => $architect->name,
            'registration_no' => $architect->registration_no,
            'specialty' => $architect->specialty,
            'firm_name' => $architect->firm_name,
        ];
    }
}
