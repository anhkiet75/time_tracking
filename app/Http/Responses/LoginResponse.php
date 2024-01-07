<?php

namespace App\Http\Responses;

use App\Filament\Admin\Resources\OrderResource;
use App\Filament\App\Resources\OrderEmployeeResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends \Filament\Http\Responses\Auth\LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect(route('filament.app.pages.dashboard'));
    }
}
