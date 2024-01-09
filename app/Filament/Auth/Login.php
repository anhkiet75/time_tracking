<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login as BaseAuth;

class Login extends BaseAuth
{
    public function mount(): void
    {
        $this->form->fill();
    }
}
