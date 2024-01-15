<?php

namespace App\Filament\Resources\TimesheetsResource\Pages;

use App\Filament\Helper\TimesheetHelper;
use App\Filament\Resources\TimesheetsResource;
use DateTime;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTimesheets extends CreateRecord
{
    protected static string $resource = TimesheetsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!$data['checkin_time'] ||  !isset($data['checkout_time'])) {
            $data['log_time'] = 0;
        } else {
            $data['log_time'] = TimesheetHelper::calculateLogTimeInMinutes(
                $data['checkin_time'],
                $data['checkout_time'],
                $data['break_time']
            );
        }
        $data['user_id'] = auth()->user()->id;
        return $data;
    }
}
