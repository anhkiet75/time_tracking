<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use Filament\Actions;
use Filament\Forms\Components\Builder;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Table;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;

class ViewLocation extends ListRecords
{
    protected static string $resource = LocationResource::class;
    protected static string $view = 'filament.resources.location-resource.pages.locations';
    use InteractsWithRecord;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('address')
            ]);
    }
}
