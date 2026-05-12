<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'المواد';
    protected static ?string $modelLabel = 'مادة';
    protected static ?string $pluralModelLabel = 'المواد';
    protected static ?string $navigationGroup = 'التصنيفات';
    protected static ?string $activeNavigationIcon = 'heroicon-o-chevron-double-down';


    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الأساسية للمادة')
                    ->description('قم بتعبئة جميع الحقول التالية :')
                    ->schema(components: [
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('minimum_quantity')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('opening_balance')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('current_quantity')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('reserved_quantity')
                            ->required()
                            ->numeric()
                            ->default(0.00),
                        Forms\Components\TextInput::make('unit')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(4),

                Forms\Components\Section::make('معلومات إضافية :')
                    ->description('')
                    ->schema(components: [
                        Forms\Components\TextInput::make('category_id')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('purchase_price')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('sale_price')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('wholesale_price')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('barcode')
                            ->maxLength(255)
                            ->default(null),

                        Forms\Components\TextInput::make('created_by')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('last_updated_by')
                            ->numeric()
                            ->default(null),
                        Forms\Components\Textarea::make('description')
                            ->columnSpan(2),
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
                Tables\Columns\TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wholesale_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('minimum_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('opening_balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reserved_quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('barcode')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_updated_by')
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
                Tables\Actions\BulkActionGroup::make([]),
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
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'الكود' => $record->code,
            'الفئة' => $record->category?->name ?? '---',
            'الوحدة' => $record->unit,
            'الكمية' => number_format($record->current_quantity, 2),
            'سعر البيع' => '$' . number_format($record->sale_price, 2),
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return self::getUrl('view', ['record' => $record]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
