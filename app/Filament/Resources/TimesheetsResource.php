<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimesheetsResource\Pages;
use App\Filament\Resources\TimesheetsResource\RelationManagers;
use App\Models\Checkin;
use App\Models\Location;
use App\Models\Timesheets;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\BooleanFilter;
use Webbingbrasil\FilamentAdvancedFilter\Filters\DateFilter;

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
                    ->required()
                    ->hiddenOn('edit'),
                Grid::make()
                    ->columnSpan(2)
                    ->schema([
                        DateTimePicker::make('checkin_time')
                            ->seconds(false)
                            ->native(false)
                            ->maxDate(now())
                            ->requiredUnless('checkout_time', null)
                            ->label('Check in time'),
                        DateTimePicker::make('checkout_time')
                            ->seconds(false)
                            ->native(false)
                            ->maxDate(now())
                            ->afterOrEqual('checkin_time')
                            ->label('Check out time'),
                    ]),
                TextInput::make('log_time')->readOnly()->hiddenOn('create'),
                TextInput::make('break_time')->suffix('minutes')->numeric()
                    ->visibleOn('edit')
                    ->hidden(fn (?Model $record) =>  isset($record->location) && !$record->location->can_break)
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
                        $checkin_time = new DateTime($record->checkin_time);
                        $checkout_time = new DateTime($record->checkout_time);
                        $interval =  $checkin_time->diff($checkout_time);
                        return sprintf(
                            '%d:%02d',
                            ($interval->days * 24) + $interval->h,
                            $interval->i
                        );
                    })
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
