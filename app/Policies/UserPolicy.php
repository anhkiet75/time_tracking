<?php

namespace App\Policies;

use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny($user)
    {
        if (Auth::guard('web')->check()) {
            return auth()->user()->is_admin;
        }
        return Auth::guard('admin')->check();
    }
}
