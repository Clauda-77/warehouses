<?php


namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\Item;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Resources\Pages\ManageRecords;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ManageBillItems extends ManageRecords
{
    protected static string $resource = BillResource::class;
    protected static string $relationship = 'billRecords';
    protected static ?string $title = 'المواد المطلوبة';
    protected static ?string $modelLabel = 'مادة';
    protected static ?string $pluralModelLabel = 'المواد';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->orderBy('id', 'asc'))
            ->defaultSort('id', 'asc')
            ->columns([
                TextColumn::make('item.code')
                    ->label('كود المادة')
                    ->searchable(),

                TextColumn::make('item.name')
                    ->label('اسم المادة')
                    ->searchable(),

                TextColumn::make('warehouse.name')
                    ->label('المستودع')
                    ->searchable(),

                TextColumn::make('item.unit')
                    ->label('الوحدة'),

                TextColumn::make('quantity')
                    ->label('الكمية')
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('unit_price')
                    ->label('السعر')
                    ->money('SDG'),

                TextColumn::make('total_price')
                    ->label('الإجمالي')
                    ->money('SDG'),
            ])

            ->headerActions([
                CreateAction::make()
                    ->label('➕ إضافة مادة جديدة')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->modalHeading('إضافة مادة إلى المذكرة')
                    ->modalSubmitActionLabel('إضافة')
                    ->modalCancelActionLabel('إلغاء')
                    ->form([
                        Forms\Components\Select::make('item_id')
                            ->label('المادة')
                            ->options(fn() => Item::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($item = Item::find($state)) {
                                    $set('unit_price', $item->sale_price ?? 0);
                                    $set('item_code', $item->code);
                                }
                            }),

                        Forms\Components\TextInput::make('quantity')
                            ->label('الكمية')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->default(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $set('total_price', ($get('unit_price') ?? 0) * $state);
                            }),

                        Forms\Components\TextInput::make('unit_price')
                            ->label('سعر الوحدة')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $set('total_price', $state * ($get('quantity') ?? 0));
                            }),

                        Forms\Components\Hidden::make('total_price'),
                    ])
                    ->action(function (array $data, $livewire) {
                        try {
                            DB::transaction(function () use ($data, $livewire) {
                                $record = $livewire->ownerRecord->billRecords()->create([
                                    'item_id' => $data['item_id'],
                                    'quantity' => $data['quantity'],
                                    'unit_price' => $data['unit_price'],
                                    'total_price' => $data['quantity'] * $data['unit_price'],
                                    'warehouse_id' => $livewire->ownerRecord->source_warehouse_id ?? Warehouse::first()?->id,
                                ]);
                            });


                            $livewire->ownerRecord->refresh();
                            $livewire->ownerRecord->updateTotals();

                            Notification::make()
                                ->title('تم إضافة المادة بنجاح')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطأ: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])

            ->actions([
                EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('الكمية')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('unit_price')
                            ->label('سعر الوحدة')
                            ->numeric()
                            ->required(),
                    ])
                    ->after(function ($record, $livewire) {
                        $record->total_price = $record->quantity * $record->unit_price;
                        $record->save();
                        $livewire->ownerRecord->refresh();
                        $livewire->ownerRecord->updateTotals();

                        Notification::make()
                            ->title('تم التعديل بنجاح')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()
                    ->label('')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->after(function ($record, $livewire) {
                        $livewire->ownerRecord->refresh();
                        $livewire->ownerRecord->updateTotals();

                        Notification::make()
                            ->title('تم الحذف بنجاح')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
