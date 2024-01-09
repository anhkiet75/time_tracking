<?php

namespace App\Policies;

use App\Models\SuperUser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user)
    {
        if (isset(auth()->user()->is_admin))
            return auth()->user()->is_admin;
        return auth()->user() instanceof SuperUser;
    }
}
