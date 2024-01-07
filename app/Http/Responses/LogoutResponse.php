<?php

namespace App\Http\Responses;

use App\Filament\Admin\Resources\OrderResource;
use App\Filament\App\Resources\OrderEmployeeResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        return redirect('/login');
    }
}
