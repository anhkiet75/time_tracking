<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): User
    {
        if (!isset($data["business_id"])) {
            $data["business_id"] = auth()->user()->business_id;
        }
        return User::create($data);
    }
}
