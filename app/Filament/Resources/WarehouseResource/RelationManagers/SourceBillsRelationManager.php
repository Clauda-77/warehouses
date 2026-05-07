<?php

namespace App\Filament\Resources\WarehouseResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SourceBillsRelationManager extends RelationManager
{
    protected static string $relationship = 'sourceBills';
    protected static ?string $recordTitleAttribute = 'bill_number';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bill_number')
                    ->label('رقم المذكرة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('التاريخ')
                    ->date(),
                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->badge(),
                Tables\Columns\TextColumn::make('destinationWarehouse.name')
                    ->label('المستودع المستهدف'),
                Tables\Columns\TextColumn::make('total')
                    ->label('الإجمالي')
                    ->money('SDG'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn($record) => BillResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
