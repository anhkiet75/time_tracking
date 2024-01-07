<?php

namespace App\Policies;

use App\Models\Checkin;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CheckinPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->allow_manual_entry;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Checkin $checkin): bool
    {
        return $user->allow_manual_entry;
    }
}
