<?php

namespace App\Filament\Admin\Resources\BusinessResource\Pages;

use App\Filament\Admin\Resources\BusinessResource;
use App\Filament\Helper\BusinessHelper;
use App\Models\Business;
use App\Models\BusinessQRCodeRange;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBusiness extends CreateRecord
{
    protected static string $resource = BusinessResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = auth()->id();
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $qr_code_ranges = BusinessHelper::convertInputToRangesArray($data['business_range']);
        $business = Business::create($data);
        BusinessHelper::createQRCodeRanges($business->id, $qr_code_ranges);
        
        return $business;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('create');
    }
}
