<?php

namespace App\Filament\Resources\TimesheetsResource\Services;

class TimesheetService
{
    public function decimalToTime($decimal)
    {
        $minute = intval($decimal);
        return sprintf(
            '%d:%02d',
            intdiv($minute, 60),
            $minute %  60
        );
    }
}
