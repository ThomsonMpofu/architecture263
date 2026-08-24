<?php

namespace App\Policies;

use App\Models\PlanApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanApplicationPolicy
{
    public function create(User $user): Response
    {
        return $user->hasRole('architect')
            ? Response::allow()
            : Response::deny('Only architects can submit plan applications.');
    }

    public function view(User $user, PlanApplication $planApplication): Response
    {
        if ($planApplication->submitted_by === $user->id) {
            return Response::allow();
        }

        // Drafts aren't submitted yet — nobody but the owning architect may see them.
        if ($planApplication->status === PlanApplication::STATUS_DRAFT) {
            return Response::deny('You do not have access to this plan application.');
        }

        if ($user->hasRole('council')) {
            return Response::allow();
        }

        if ($planApplication->engagement && $planApplication->engagement->client_id === $user->id) {
            return Response::allow();
        }

        return Response::deny('You do not have access to this plan application.');
    }

    public function resubmit(User $user, PlanApplication $planApplication): Response
    {
        return $planApplication->submitted_by === $user->id
            ? Response::allow()
            : Response::deny('Only the submitting architect can resubmit this application.');
    }

    public function comment(User $user, PlanApplication $planApplication): Response
    {
        return $user->hasRole('council')
            ? Response::allow()
            : Response::deny('Only council reviewers can add technical review comments.');
    }

    public function decide(User $user, PlanApplication $planApplication): Response
    {
        return $user->hasRole('council')
            ? Response::allow()
            : Response::deny('Only council reviewers can decide on plan applications.');
    }
}
