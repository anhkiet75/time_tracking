<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Tables\Columns;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\Type\TrueType;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $recordTitleAttribute = 'qr_code';
    protected static ?string $recordRouteKeyName = 'qr_code';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Map::make('location')
                    ->autocomplete(
                        fieldName: 'address',
                    )
                    ->autocompleteReverse(true)->hiddenOn('view'),
                Textarea::make('address')
                    ->required(),
                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        TextInput::make('qr_code')
                            ->label('QR code')
                            ->required()
                            ->columnSpan(1),
                        Grid::make()
                            ->columns(3)
                            ->schema([
                                Toggle::make('can_logtime')->label('Check in/ Check out')->default(true),
                                Toggle::make('can_check')->label('Check point')->default(true),
                                Toggle::make('enable_gps')->label('Forces enable GPS')->default(true),
                            ])->columnSpan(2),
                    ]),
                Section::make()
                    ->columns(1)
                    ->schema([
                        Repeater::make('subLocations')
                            ->relationship()
                            ->grid(3)
                            ->schema([
                                TextInput::make('name')->required(),
                                TextInput::make('qr_code')->required()->distinct(),
                                Toggle::make('can_logtime')->label('Check in/ Check out')->default(true),
                                Toggle::make('can_check')->label('Check point')->default(true),
                                Toggle::make('enable_gps')->label('Forces enable GPS')->default(true),
                            ]),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('qr_code')->label('QR code'),
                TextColumn::make('address'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record:qr_code}/edit'),
            'view' => Pages\ViewLocation::route('/{record:qr_code}/view')
        ];
    }
}
