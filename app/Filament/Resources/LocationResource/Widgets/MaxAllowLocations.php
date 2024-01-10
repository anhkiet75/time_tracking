<?php

namespace App\Filament\Resources\LocationResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class MaxAllowLocations extends BaseWidget
{
    public ?Model $record = null;
    protected function getStats(): array
    {
        return [
        ];
    }
}
