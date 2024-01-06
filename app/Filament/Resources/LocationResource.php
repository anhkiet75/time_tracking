<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Location;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Tables\Columns;
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
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('qr_code')
                            ->label('QR code')
                            ->required()
                            ->columnSpan(1),
                        Repeater::make('subLocations')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')->required(),
                                TextInput::make('qr_code')->required(),
                                TextInput::make('business_id')->default(auth()->user()->business_id)->readOnly()->hidden(),
                            ])
                            ->columns(2)
                            ->columnSpan(2)
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
