<?php

namespace App\Filament\Admin\Resources\UserAdminResource\Pages;

use App\Filament\Admin\Resources\UserAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserAdmin extends CreateRecord
{
    protected static string $resource = UserAdminResource::class;
}
