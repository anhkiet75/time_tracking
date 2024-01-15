<?php

namespace App\Filament\Resources\TimesheetsResource\Pages;

use App\Filament\Helper\TimesheetHelper;
use App\Filament\Resources\TimesheetsResource;
use DateTime;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimesheets extends EditRecord
{
    protected static string $resource = TimesheetsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['log_time'] = TimesheetHelper::calculateLogTimeInString($data['log_time']);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['log_time'] = TimesheetHelper::calculateLogTimeInMinutes(
            $data['checkin_time'],
            $data['checkout_time'],
            $data['break_time']
        );
        return $data;
    }
}
