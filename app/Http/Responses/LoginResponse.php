<?php

namespace App\Http\Responses;

use App\Filament\Admin\Resources\OrderResource;
use App\Filament\App\Resources\OrderEmployeeResource;
use App\Models\SuperAdmin;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends \Filament\Http\Responses\Auth\LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (Filament::getCurrentPanel()->getId() == 'admin') {
            return redirect(route('filament.admin.resources.businesses.index'));
        }
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
