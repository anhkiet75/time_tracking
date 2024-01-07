<?php

namespace App\Filament\Resources\TimesheetsResource\Pages;

use App\Filament\Resources\TimesheetsResource;
use App\Filament\Resources\TimesheetsResource\Widgets\TimesheetOverview;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetsResource::class;

    public static function getWidgets(): array
    {
        return [
            TimesheetOverview::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TimesheetOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'this_week' =>  Tab::make('This week')->query(fn ($query) => $query->thisWeek()),
            'last_week' => Tab::make('Last week')->query(fn ($query) => $query->lastWeek()),
            'last_month' => Tab::make('Last month')->query(fn ($query) => $query->lastMonth()),
            'last_quarter' => Tab::make('Last quarter')->query(fn ($query) => $query->lastQuarter()),
            'this_year' => Tab::make('This year')->query(fn ($query) => $query->thisYear()),
            'all' => Tab::make('All'),
        ];
    }
}
