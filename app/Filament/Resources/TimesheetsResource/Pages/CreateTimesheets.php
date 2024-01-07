<?php

namespace App\Filament\Resources\TimesheetsResource\Pages;

use App\Filament\Resources\TimesheetsResource;
use DateTime;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTimesheets extends CreateRecord
{
    protected static string $resource = TimesheetsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $checkin_time = new DateTime($data['checkin_time']);
        $checkout_time = new DateTime($data['checkout_time']);
        $interval =  $checkin_time->diff($checkout_time);
        $data['log_time'] = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
        $data['user_id'] = auth()->user()->id;
        return $data;
    }
}
