<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'المستودعات';
    protected static ?string $modelLabel = 'مستودع';
    protected static ?string $pluralModelLabel = 'المستودعات';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chevron-double-down';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('المعلومات الأساسية:')
                    ->description('قم بتعبئة الحقول التالية :')
                    ->schema(components: [
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('type')
                            ->required()
                            ->maxLength(255)
                            ->default('central'),
                        Forms\Components\TextInput::make('total_items')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('total_value')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                    ])->columns(3),
                Forms\Components\Section::make()
                    // ->description('قم بتعبئة الحقول التالية :')
                    ->schema(components: [
                        Forms\Components\TextInput::make('contact_person')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\Textarea::make('location')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->required(),
                    ])->columns(2),



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
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('total_items')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_value')
                    ->numeric()
                    ->sortable(),
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
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'الكود' => $record->code,
            'النوع' => WarehouseType::tryFrom($record->type)?->label() ?? $record->type,
            'الموقع' => $record->location ? substr($record->location, 0, 50) : '---',
            'الأصناف' => $record->total_items,
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return self::getUrl('view', ['record' => $record]);
    }
}
