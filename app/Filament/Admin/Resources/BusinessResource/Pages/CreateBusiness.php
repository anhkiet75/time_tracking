<?php

namespace App\Filament\Admin\Resources\BusinessResource\Pages;

use App\Filament\Admin\Resources\BusinessResource;
use App\Models\Business;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBusiness extends CreateRecord
{
    protected static string $resource = BusinessResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = auth()->id();
        return $data;
    }

    protected function handleRecordCreation(array $data): Business
    {
        $business = Business::create($data);
        $data["name"] = $data["username"];
        $data["is_admin"] = true;
        $data["business_id"] = $business->id;
        $data["pin_code"] = $data["pin_code"];
        User::create($data);
        return $business;
    }
}
