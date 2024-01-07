<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Checkin;
use App\Models\Location;
use App\Models\User;
use App\Policies\CheckinPolicy;
use App\Policies\LocationPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Location::class => LocationPolicy::class,
        User::class => UserPolicy::class,
        Checkin::class => CheckinPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
    }
}
