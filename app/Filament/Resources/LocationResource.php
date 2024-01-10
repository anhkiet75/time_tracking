<?php

namespace App\Filament\Resources;

use App\Filament\Helper\BusinessHelper;
use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Business;
use App\Models\Location;
use App\Models\User;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Tables\Columns;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\Type\TrueType;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $recordTitleAttribute = 'qr_code';
    protected static ?string $recordRouteKeyName = 'qr_code';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static $lat = null;
    protected static $lng = null;

    public static function form(Form $form): Form
    {
        $location = $form->getRecord();
        self::$lat = 10;
        self::$lng = 10;
        if (isset($location)) {
            self::$lat = $location->lat;
            self::$lng = $location->lng;
        }
        return $form
            ->schema([
                Map::make('location')
                    ->autocomplete(
                        fieldName: 'address',
                    )
                    ->defaultLocation([self::$lat, self::$lng])
                    ->autocompleteReverse(true)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        self::$lat = $state['lat'];
                        self::$lng = $state['lng'];
                    })
                    ->hiddenOn('view'),
                Textarea::make('address')
                    ->required(),
                Textarea::make('name'),
                TextInput::make('radius')
                    ->numeric()
                    ->default(1000)
                    ->suffix('meters')
                    ->required(),
                Section::make('Settings')
                    ->columns(2)
                    ->schema([
                        TextInput::make('qr_code')
                            ->label('QR code')
                            ->required()
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, Closure $fail) {
                                        if (!BusinessHelper::validateRangeInternal(auth()->user()->business->id, $value)) {
                                            $fail('The QR code is invalid.');
                                        }
                                    };
                                },
                            ])
                            ->columnSpan(1),
                        Placeholder::make('Business')
                            ->label('Business QR code ranges')
                            ->content(fn () => Auth::user()->business->business_range),
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                Toggle::make('can_logtime')->label('Allow check in/check out')->default(true),
                                // Toggle::make('can_check')->label('Allow check checkpoint')->default(true),
                                Toggle::make('enable_gps')->label('Forces enable GPS')->default(true),
                                Toggle::make('can_break')->label('Allow add break time')->default(true)
                            ])->columnSpan(2),
                    ]),
                Section::make()
                    ->columns(1)
                    ->schema([
                        Repeater::make('subLocations')
                            ->relationship()
                            ->grid(2)
                            ->defaultItems(0)
                            ->schema([
                                TextInput::make('name')->required(),
                                TextInput::make('qr_code')->required()->distinct()
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, Closure $fail) {
                                                if (!BusinessHelper::validateRangeInternal(auth()->user()->business->id, $value)) {
                                                    $fail('The QR code is invalid.');
                                                }
                                            };
                                        },
                                    ]),
                                Toggle::make('can_logtime')->label('Check in/ Check out')->default(true),
                                Toggle::make('can_check')->label('Check point')->default(true),
                                Toggle::make('enable_gps')->label('Forces enable GPS')->default(true),
                            ])
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, callable $get): array {
                                $data['lat'] = $get('location')['lat'];
                                $data['lng'] = $get('location')['lng'];
                                $data['radius'] = $get('radius');
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, callable $get): array {
                                $data['lat'] = $get('location')['lat'];
                                $data['lng'] = $get('location')['lng'];
                                $data['radius'] = $get('radius');
                                return $data;
                            }),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('qr_code')->label('QR code'),
                TextColumn::make('name'),
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
