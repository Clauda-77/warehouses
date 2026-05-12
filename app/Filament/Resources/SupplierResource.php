<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use App\Filament\Resources;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'الموردين';
    protected static ?string $modelLabel = 'مورد';
    protected static ?string $pluralModelLabel = 'الموردين';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chevron-double-down';

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الشخصية')
                    ->description('قم بتعبئة الحقول التالية :')
                    ->schema(components: [
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('الرمز'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('الاسم'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->placeholder('09')
                            ->default(null),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\Textarea::make('address')
                            ->placeholder('عنوان المورد')
                            ->columnSpanFull(),
                    ])->columns(4),

                Forms\Components\Section::make('معلومات أخرى: ')
                    // ->description('قم بتعبئة الحقول التالية :')
                    ->schema(components: [
                        Forms\Components\TextInput::make('tax_number')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('commercial_register')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('balance')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('total_purchases')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('contact_person')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('commercial_register')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_purchases')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(' تم الحذف بنجاح.')
                            ->icon('heroicon-o-x-circle')
                            ->color('danger')
                    )
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
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationGroup(): ?string
    {
        return 'إدارة النظام';
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
