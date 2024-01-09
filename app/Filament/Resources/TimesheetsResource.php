<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimesheetsResource\Pages;
use App\Filament\Resources\TimesheetsResource\RelationManagers;
use App\Models\Checkin;
use App\Models\Timesheets;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                DateTimePicker::make('checkin_time')
                    ->seconds(false)
                    ->native(false)
                    ->maxDate(now()),
                DateTimePicker::make('checkout_time')
                    ->seconds(false)
                    ->native(false)
                    ->maxDate(now())
                    ->afterOrEqual('checkin_time'),
                Select::make('location_id')
                    ->relationship('location', 'address')
                    ->required()
                    ->hiddenOn('edit'),
                TextInput::make('log_time')->readOnly()->hiddenOn('create'),
                TextInput::make('break_time')->numeric()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('checkin_time')->datetime('H:i m-d-Y'),
                TextColumn::make('checkout_time')->datetime('H:i m-d-Y'),
                TextColumn::make('checkpoint_time')->datetime('H:i m-d-Y')->label('Check time'),
                TextColumn::make('break_time'),
                TextColumn::make('location.name'),
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
            ->defaultSort('id', 'desc')
            ->filters([
                //
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
