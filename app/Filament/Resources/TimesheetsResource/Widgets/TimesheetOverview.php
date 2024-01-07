<?php

namespace App\Filament\Resources\TimesheetsResource\Widgets;

use App\Filament\Resources\TimesheetsResource\Services\TimesheetService;
use App\Models\Checkin;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TimesheetOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $timesheetsThisWeek = Checkin::query()->thisWeek()->sum('log_time');
        $timesheetsThisMonth = Checkin::query()->thisMonth()->sum('log_time');
        $timesheetsThisQuater = Checkin::query()->thisQuarter()->sum('log_time');

        return [
            Stat::make('This week', (new TimesheetService)->decimalToTime($timesheetsThisWeek)),
            Stat::make('This month', (new TimesheetService)->decimalToTime($timesheetsThisMonth)),
            Stat::make('This quarter', (new TimesheetService)->decimalToTime($timesheetsThisQuater)),
        ];
    }
}
