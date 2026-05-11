<?php

namespace App\Filament\Resources\WarehouseResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items'; // العلاقة many-to-many عبر warehouse_stocks

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الصنف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pivot.current_quantity')
                    ->label('الكمية الحالية')
                    ->numeric(),
                Tables\Columns\TextColumn::make('pivot.average_cost')
                    ->label('متوسط التكلفة')
                    ->money('SDG'),
                Tables\Columns\TextColumn::make('unit')
                    ->label('الوحدة'),
                Tables\Columns\TextColumn::make('pivot.minimum_quantity')
                    ->label('الحد الأدنى'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('إضافة صنف')
                    ->preloadRecordSelect()
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('current_quantity')
                            ->label('الكمية الافتتاحية')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('average_cost')
                            ->label('متوسط التكلفة')
                            ->numeric()
                            ->default(0),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('current_quantity')
                            ->label('الكمية الحالية')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('average_cost')
                            ->label('متوسط التكلفة')
                            ->numeric(),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}
