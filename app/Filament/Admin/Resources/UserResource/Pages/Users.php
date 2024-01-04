<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\Page;

class Users extends Page
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.admin.resources.user-resource.pages.users';

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }
}
