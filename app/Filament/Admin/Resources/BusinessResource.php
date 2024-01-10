<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BusinessResource\Pages;
use App\Filament\Admin\Resources\BusinessResource\RelationManagers;
use App\Filament\Helper\BusinessHelper;
use App\Models\Business;
use App\Models\User;
use Closure;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('address')
                    ->maxLength(255),
                TextInput::make('company_business_number')
                    ->maxLength(255),
                TextInput::make('phone_number')
                    ->maxLength(20),
                TextInput::make('business_range')
                    ->label('Business QR code range')
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Example: 100-200,200-300')
                    ->required()
                    ->rules([
                        function (?Model $record) {
                            return function (string $attribute, $value, Closure $fail) use ($record) {
                                if (isset($record)  && $value != $record->business_range) {
                                    $qr_code_ranges = BusinessHelper::convertInputToRangesArray($value);
                                    if (!BusinessHelper::validateRange($qr_code_ranges)) {
                                        $fail('The :attribute is invalid.');
                                    }
                                }
                            };
                        },
                    ]),
                TextInput::make('max_allow_locations')->numeric()->rules(['min:1']),
                Fieldset::make('Admin account')
                    ->relationship('user')
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('pin_code')
                            ->length(6)
                            ->numeric()
                            ->required(),
                        Hidden::make('is_admin')->default(true),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('password')
                            ->maxLength(255)
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state)),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_business_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('admin_id')->hidden()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('Login')
                    ->color('warning')
                    ->icon('heroicon-o-information-circle')
                    ->action(function (Get $get, ?Model $record) {
                        $id = $record["admin_id"];
                        $user = User::find($id);
                        Auth::guard('web')->login($user);
                        redirect()->route('filament.app.pages.dashboard');
                    })
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
            'index' => Pages\ListBusinesses::route('/'),
            'create' => Pages\CreateBusiness::route('/create'),
            'edit' => Pages\EditBusiness::route('/{record}/edit'),
        ];
    }
}
