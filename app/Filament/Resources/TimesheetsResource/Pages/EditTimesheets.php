<?php

namespace App\Filament\Resources\TimesheetsResource\Pages;

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
        $data['log_time'] =
            sprintf(
                '%d:%02d',
                intdiv($data['log_time'], 60),
                $data['log_time'] %  60
            );
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $checkin_time = new DateTime($data['checkin_time']);
        $checkout_time = new DateTime($data['checkout_time']);
        $interval =  $checkin_time->diff($checkout_time);
        $data['log_time'] = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

        return $data;
    }
}
