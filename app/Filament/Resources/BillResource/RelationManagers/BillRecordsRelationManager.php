<?php

namespace App\Filament\Resources\BillResource\RelationManagers;

use App\Models\Item;
use App\Enums\BillType;
use App\Enums\BillStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class BillRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'billRecords';
    protected static ?string $title = 'المواد المطلوبة';
    protected static ?string $modelLabel = 'مادة';
    protected static ?string $pluralModelLabel = 'المواد';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(9)
                    ->schema([
                        Forms\Components\TextInput::make('item_code')
                            ->label('رقم المادة')
                            ->columnSpan(1)
                            ->maxLength(50)
                            ->default(fn ($get) => $this->getItemCode($get('item_id')))
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('batch_number')
                            ->label('رقم البطاقة')
                            ->columnSpan(1)
                            ->maxLength(50)
                            ->nullable(),

                        Forms\Components\Select::make('item_id')
                            ->label('اسم المادة')
                            ->columnSpan(2)
                            ->options(function () {
                                return Item::pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state && $item = Item::find($state)) {
                                    $set('item_code', $item->code);
                                    $set('unit_price', $item->sale_price);
                                }
                            }),

                        Forms\Components\TextInput::make('unit')
                            ->label('الوحدة')
                            ->columnSpan(1)
                            ->default('عدد')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('الكمية')
                            ->columnSpan(1)
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $unitPrice = $get('unit_price') ?? 0;
                                $set('total_price', $unitPrice * $state);
                            }),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('السعر')
                            ->columnSpan(1)
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $quantity = $get('quantity') ?? 0;
                                $set('total_price', $state * $quantity);
                            }),

                        Forms\Components\TextInput::make('total_price')
                            ->label('القيمة')
                            ->columnSpan(1)
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Hidden::make('warehouse_id')
                            ->default(fn () => $this->getWarehouseId()),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item.name')
            ->columns([
                Tables\Columns\TextColumn::make('item_code')
                    ->label('رقم المادة')
                    ->searchable(),
//                    ->sortable(),

                Tables\Columns\TextColumn::make('batch_number')
                    ->label('رقم البطاقة')
                    ->searchable(),
//                    ->sortable(),

                Tables\Columns\TextColumn::make('item.name')
                    ->label('اسم المادة')
                    ->searchable(),
//                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية')
                    ->numeric(decimalPlaces: 2),
//                    ->sortable(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->label('السعر')
                    ->money('SDG'),
//                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('القيمة')
                    ->money('SDG')
//                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('المجموع')
                            ->money('SDG'),
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y H:i')
//                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة مادة')
                    ->modalHeading('إضافة مادة جديدة')
                    ->modalSubmitActionLabel('إضافة')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn () => $this->ownerRecord->status === BillStatus::DRAFT->value)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['warehouse_id'] = $this->getWarehouseId();
                        $data['unit'] = 'عدد';
                        return $data;
                    })
                    ->after(function () {
                        $this->ownerRecord->refresh();
                        $this->ownerRecord->updateTotals();

                        Notification::make()
                            ->title('تم إضافة المادة بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->visible(fn ($record) => $record->bill->status === BillStatus::DRAFT->value)
                    ->after(function () {
                        $this->ownerRecord->refresh();
                        $this->ownerRecord->updateTotals();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->visible(fn ($record) => $record->bill->status === BillStatus::DRAFT->value)
                    ->after(function () {
                        $this->ownerRecord->refresh();
                        $this->ownerRecord->updateTotals();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد')
                        ->visible(fn () => $this->ownerRecord->status === BillStatus::DRAFT->value)
                        ->after(function () {
                            $this->ownerRecord->refresh();
                            $this->ownerRecord->updateTotals();
                        }),
                ]),
            ]);
    }

    private function getWarehouseId(): ?int
    {
        return match($this->ownerRecord->type) {
            BillType::PURCHASE->value, BillType::RETURN->value => $this->ownerRecord->destination_warehouse_id,
            BillType::TRANSFER->value, BillType::ADJUSTMENT->value => $this->ownerRecord->source_warehouse_id,
            default => null,
        };
    }

    private function getItemCode($itemId): ?string
    {
        if ($itemId && $item = Item::find($itemId)) {
            return $item->code;
        }
        return null;
    }
}
