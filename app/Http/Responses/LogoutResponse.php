<?php

namespace App\Http\Responses;

use App\Filament\Admin\Resources\OrderResource;
use App\Filament\App\Resources\OrderEmployeeResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Filament\Facades\Filament;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        if (Filament::getCurrentPanel()->getId() == 'admin') {
            return redirect(route('filament.admin.auth.login'));
        }
        return redirect(route('filament.app.auth.login'));
    }
}
