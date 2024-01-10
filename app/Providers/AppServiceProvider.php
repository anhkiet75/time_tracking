<?php

namespace App\Providers;

use App\Filament\Resources\LocationResource\Pages\ListLocations;
use App\Models\Location;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as ContractsLogoutResponse;
use Filament\Resources\Pages\ListRecords;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
        $this->app->bind(ContractsLogoutResponse::class, \App\Http\Responses\LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
       }
    }
}
