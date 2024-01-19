<?php

namespace App\Filament\Resources;

use App\Filament\Helper\BusinessHelper;
use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Models\Business;
use App\Models\BusinessQRCodeRange;
use App\Models\Location;
use App\Models\User;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Filters\RadiusFilter;
use Closure;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
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
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\Type\TrueType;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;
    protected static ?string $recordTitleAttribute = 'qr_code';
    protected static ?string $recordRouteKeyName = 'qr_code';
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected ?string $heading = 'Custom Page Heading';
    protected static $lat = null;
    protected static $lng = null;

    public static function form(Form $form): Form
    {
        $location = $form->getRecord();
        $business = Business::find(auth()->user()->business_id);
        $max_allow_locations = $business->max_allow_locations ?? 1000;
        $available_locations = $max_allow_locations - $business->locations->count();
        if (isset($location)) {
            if ($location->is_sub_location) $available_locations = 0;
            else {
                $number_of_current_sub_locations = $business->locations->where('parent_id', $location->id)->count();
                $available_locations = $available_locations + $number_of_current_sub_locations;
            }
        } else  $available_locations = $available_locations - 1;
        self::$lat = 10;
        self::$lng = 10;
        if (isset($location)) {
            self::$lat = $location->lat;
            self::$lng = $location->lng;
        }

        $getNextAvailable = function () {
            $min_qr_code = $_COOKIE['min_qr_code'] ?? 0;
            $qr_code_ranges = BusinessQRCodeRange::where('business_id', auth()->user()->business_id)
                ->orderBy('start_range')->get();
            foreach ($qr_code_ranges as $range)
                for ($i = $range->start_range; $i <= $range->end_range; $i++) {
                    if ($i > $min_qr_code && !Location::withoutGlobalScopes()->where('qr_code', $i)->exists()) {
                        $min_qr_code = $i;
                        setcookie('min_qr_code', $min_qr_code, time() + 3600, "/");
                        return $i;
                    }
                }
            return '';
        };

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
                Textarea::make('name')->maxLength(255)->required(),
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
                            ->columnSpan(1)
                            ->default($getNextAvailable())
                            ->required()
                            ->rules([
                                function (?Model $record) {
                                    return function (string $attribute, $value, Closure $fail) use ($record) {
                                        if (!BusinessHelper::validateRangeInternal(auth()->user()->business->id, $value)) {
                                            $fail('The QR code is not in QR code ranges');
                                        }
                                        $location = Location::where('qr_code', $value);
                                        if ($location->exists()) {
                                            if (isset($record)) {
                                                if ($record->id != $location->first()->id)
                                                    $fail("This QR is already in use on your location ({$location->first()->name}). Please use another QR that is not assigned to a location.");
                                            } else
                                                $fail("This QR is already in use on your location ({$location->first()->name}). Please use another QR that is not assigned to a location.");
                                        }
                                    };
                                },
                            ]),
                        Placeholder::make('Business')
                            ->label('Business QR code ranges')
                            ->content(fn() => Auth::user()->business->business_range),
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                Toggle::make('can_logtime')->label('Allow log time')->default(true),
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
                                TextInput::make('qr_code')
                                    ->required()
                                    ->default($getNextAvailable())
                                    ->label('QR code')
                                    ->rules([
                                        function (?Model $record) {
                                            return function (string $attribute, $value, Closure $fail) use ($record) {
                                                if (!BusinessHelper::validateRangeInternal(auth()->user()->business->id, $value)) {
                                                    $fail('The QR code is not in QR code ranges');
                                                }
                                                $location = Location::where('qr_code', $value);
                                                if ($location->exists()) {
                                                    if (isset($record)) {
                                                        if ($record->id != $location->first()->id)
                                                            $fail("This QR is already in use on your location ({$location->first()->name}). Please use another QR that is not assigned to a location.");
                                                    } else
                                                        $fail("This QR is already in use on your location ({$location->first()->name}). Please use another QR that is not assigned to a location.");
                                                }
                                            };
                                        },
                                    ]),
                                Toggle::make('can_logtime')->label('Check log time')->default(true),
                                Toggle::make('can_check')->label('Check point')->default(true),
                                Toggle::make('enable_gps')->label('Forces enable GPS')->default(true),
                                Hidden::make('is_sub_location')->default(true)
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
                            })
                            ->maxItems($available_locations)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_sub_location')
                    ->boolean()
                    ->trueIcon('heroicon-s-chevron-right')
                    ->falseIcon('heroicon-m-list-bullet')
                    ->size(IconColumn\IconColumnSize::Small)
                    ->trueColor('warning')
                    ->falseColor('info')
                    ->label('')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('qr_code')
                    ->label('QR code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('name')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('address')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                ToggleColumn::make('can_logtime')
                    ->sortable()
                    ->label('Allow log time')
                    ->toggleable(isToggledHiddenByDefault: false),
                ToggleColumn::make('enable_gps')
                    ->sortable()
                    ->label('Forces enable GPS')
                    ->toggleable(isToggledHiddenByDefault: false),
                ToggleColumn::make('can_break')
                    ->sortable()
                    ->label('Allow add break time')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->groups([
                Group::make('parentLocation.name')
                    ->label('Main location')
                    ->collapsible(),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderByRaw('IFNULL(parent_id,id), parent_id asc');
            })
            ->filters([
                TernaryFilter::make('is_sub_location')
                    ->label('Sub location'),
                TernaryFilter::make('can_logtime')
                    ->label('Allow log time'),
                TernaryFilter::make('enable_gps')
                    ->label('Forces enable GPS'),
                TernaryFilter::make('can_check')
                    ->label('Allow add break time')
            ])
            ->actions([
                Action::make('Filter')
                    ->color('gray')
                    ->iconButton()
                    ->icon('heroicon-s-magnifying-glass')
                    ->size(ActionSize::Small)
                    ->iconPosition(IconPosition::After)
                    ->url(fn(Location $location): string => route('filament.app.resources.timesheet.index', ['tableFilters[location][values][0]' => $location->id])),
                Tables\Actions\EditAction::make()
                    ->button()
                    ->size(ActionSize::Small)
                    ->iconPosition(IconPosition::After),
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
            'edit' => Pages\EditLocation::route('/{record:qr_code}/edit')
        ];
    }
}
