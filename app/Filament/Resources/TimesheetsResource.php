<?php

namespace App\Filament\Resources;

use App\Filament\Helper\TimesheetHelper;
use App\Filament\Resources\TimesheetsResource\Pages;
use App\Filament\Resources\TimesheetsResource\RelationManagers;
use App\Models\Checkin;
use App\Models\Location;
use App\Models\Timesheets;
use Closure;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\BooleanFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;
use Illuminate\Support\Arr;

class TimesheetsResource extends Resource
{
    protected static ?string $model = Checkin::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $slug = 'timesheet';
    protected static ?string $pluralModelLabel = 'timesheet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => ($record->parentLocation ? ($record->parentLocation->name . " - ") : "") . "{$record->name}")
                    ->hiddenOn('edit'),
                Grid::make([
                    'md' => 3,
                    'lg' => 3,
                    'xl' => 3,
                    'sm' => 1
                ])
                    ->schema([
                        DateTimePicker::make('checkin_time')
                            ->seconds(false)
                            ->native(false)
                            ->maxDate(now())
                            ->requiredUnless('checkout_time', null)
                            ->label('Check in time'),
                        TextInput::make('break_time')
                            ->suffix('minutes')
                            ->default(0)
                            ->numeric()
                            ->rules(['integer'])
                            ->rules([
                                fn (Get $get) =>
                                function (string $attribute, $value, Closure $fail) use ($get) {
                                    if (TimesheetHelper::calculateLogTimeInMinutes(
                                        $get('checkin_time'),
                                        $get('checkout_time'),
                                        $value
                                    ) < 0) {
                                        $fail('The :attribute is invalid.');
                                    }
                                }
                            ]),
                        DateTimePicker::make('checkout_time')
                            ->seconds(false)
                            ->native(false)
                            ->maxDate(now())
                            ->afterOrEqual('checkin_time')
                            ->label('Check out time'),
                    ]),
                TextInput::make('log_time')->readOnly()->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->sortable()
                    ->hidden(!auth()->user()->is_admin)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('checkin_time')
                    ->sortable()
                    ->datetime('H:i m-d-Y')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('checkout_time')
                    ->sortable()
                    ->datetime('H:i m-d-Y')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('checkpoint_time')
                    ->datetime('H:i m-d-Y')
                    ->sortable()
                    ->label('Check time')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('break_time')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('location.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('log_time')
                    ->state(function (Checkin $record) {
                        if (!isset($record->checkin_time) || !isset($record->checkout_time))
                            return '00:00';
                        $log_time = TimesheetHelper::calculateLogTimeInMinutes($record->checkin_time, $record->checkout_time, $record->break_time);
                        return TimesheetHelper::calculateLogTimeInString($log_time);
                    })
                    ->summarize(Sum::make()->label('Total')->formatStateUsing(
                        static function ($state) {
                            $isArrayState = is_array($state);

                            $state = array_map(function ($state) {
                                if (blank($state)) {
                                    return null;
                                }

                                return TimesheetHelper::calculateLogTimeInString($state);
                            }, Arr::wrap($state));

                            if (!$isArrayState) {
                                return $state[0];
                            }

                            return $state;
                        }
                    ))
            ])
            ->filters([
                DateFilter::make('checkin_time'),
                DateRangeFilter::make('checkin_time')->label('Check in time range'),
                DateRangeFilter::make('checkout_time')->label('Check out time range'),
                DateRangeFilter::make('checkpoint_time')->label('Check time range'),
                SelectFilter::make('location')
                    ->relationship('location', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->hidden(!auth()->user()->is_admin)

            ], layout: FiltersLayout::AboveContent)
            ->actions([
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
            'index' => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheets::route('/create'),
            'edit' => Pages\EditTimesheets::route('/{record}/edit'),
        ];
    }
}
