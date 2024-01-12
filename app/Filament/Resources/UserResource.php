<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'employees';
    protected static ?string $modelLabel = 'employees';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),
                TextInput::make('password')->password()->required()->hiddenOn('edit'),
                DatePicker::make('birthdate'),
                FileUpload::make('image_path')->label('image'),
                Fieldset::make('Settings')
                    ->schema([
                        TextInput::make('pin_code')
                            ->length(6)
                            ->unique(ignoreRecord: true)
                            ->numeric()
                            ->required(),

                        Grid::make()
                            ->schema([
                                Toggle::make('allow_manual_entry')
                                    ->default(true),
                                Toggle::make('allow_qr_code_entry')
                                    ->label('Allow QR code entry')
                                    ->default(true)
                            ])
                            ->columns(1)
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('business_id', auth()->user()->business_id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email'),
                TextColumn::make('birthdate')
                    ->sortable(),
                TextColumn::make('pin_code'),
                IconColumn::make('is_admin')->boolean()->label('Role admin'),
                ImageColumn::make('image_path')->label('Avatar'),
                ToggleColumn::make('allow_manual_entry')
                    ->sortable(),
                ToggleColumn::make('allow_qr_code_entry')
                    ->sortable()
                    ->label('Allow QR code entry'),
            ])
            ->filters([
                SelectFilter::make('allow_manual_entry')
                    ->options([
                        'true' => 'True',
                        'false' => 'False',
                    ]),
                SelectFilter::make('allow_qr_code_entry')
                    ->label('Allow QR code entry')
                    ->options([
                        'true' => 'True',
                        'false' => 'False',
                    ]),
            ])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
