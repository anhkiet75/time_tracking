<?php

namespace App\Http\Responses;

use App\Filament\Admin\Resources\OrderResource;
use App\Filament\App\Resources\OrderEmployeeResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends \Filament\Http\Responses\Auth\LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = User::find(Auth::id());
        if (isset($user->use_pin_code)) {
            $user->use_pin_code = false;
            $user->save();
        }
        if (auth()->user() instanceof User)
            return redirect(route('filament.app.pages.dashboard'));
        return redirect(route('filament.admin.auth.login'));
    }
}
