<?php

namespace App\Policies;

use App\Models\Engagement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EngagementPolicy
{
    public function create(User $user): Response
    {
        return $user->hasRole('client')
            ? Response::allow()
            : Response::deny('Only clients can select an architect.');
    }

    public function view(User $user, Engagement $engagement): Response
    {
        return $this->isParty($user, $engagement)
            ? Response::allow()
            : Response::deny('You are not a party to this engagement.');
    }

    public function approve(User $user, Engagement $engagement): Response
    {
        return $engagement->architect_id === $user->id
            ? Response::allow()
            : Response::deny('Only the selected architect can approve this engagement.');
    }

    public function signContract(User $user, Engagement $engagement): Response
    {
        return $this->isParty($user, $engagement)
            ? Response::allow()
            : Response::deny('You are not a party to this engagement.');
    }

    private function isParty(User $user, Engagement $engagement): bool
    {
        return $engagement->client_id === $user->id || $engagement->architect_id === $user->id;
    }
}
