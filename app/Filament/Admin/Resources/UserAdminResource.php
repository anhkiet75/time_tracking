<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserAdminResource\Pages;
use App\Filament\Admin\Resources\UserAdminResource\RelationManagers;
use App\Models\User;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserAdminResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $slug = 'users';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->hiddenOn('edit'),
                DatePicker::make('birthdate'),
                Select::make('business_id')
                    ->relationship('business', 'name')
                    ->required(),
                Grid::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('pin_code')
                            ->length(6)
                            ->numeric()
                            ->required()
                            ->columnSpan(1),
                        Grid::make()
                            ->columns(1)
                            ->schema([
                                Toggle::make('allow_manual_entry'),
                                Toggle::make('allow_qr_code_entry')
                                    ->label('Allow QR code entry')
                                    ->default(true),
                            ])->columnSpan(1),
                    ]),
                FileUpload::make('image_path')->label('image')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('birthdate'),
                TextColumn::make('business.name'),
                IconColumn::make('is_admin')->boolean()
            ])
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
            'index' => Pages\ListUserAdmins::route('/'),
            'create' => Pages\CreateUserAdmin::route('/create'),
            'edit' => Pages\EditUserAdmin::route('/{record}/edit'),
        ];
    }
}
