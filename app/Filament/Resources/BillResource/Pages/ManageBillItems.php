<?php
////////
////////namespace App\Filament\Resources\BillResource\Pages;
////////
////////use App\Filament\Resources\BillResource;
////////use App\Models\Bill;
////////use App\Models\Item;
////////use App\Models\BillRecord;
////////use App\Models\Warehouse;
////////use Filament\Forms;
////////use Filament\Forms\Form;
////////use Filament\Actions;
////////use Filament\Resources\Pages\Page;
////////use Filament\Notifications\Notification;
////////use Illuminate\Support\Facades\DB;
////////
////////class ManageBillItems extends Page
////////{
////////    public Bill $record;
////////
////////    protected static string $resource = BillResource::class;
////////    protected static string $view = 'filament.resources.bill-resource.pages.manage-bill-items';
////////
////////    public function mount($record): void
////////    {
////////        $this->record = Bill::findOrFail($record);
////////    }
////////
////////    public function form(Form $form): Form
////////    {
////////        return $form
////////            ->schema([
////////
////////            ]);
////////    }
////////
////////    protected function getHeaderActions(): array
////////    {
////////        return [
////////            Actions\Action::make('back_to_bill')
////////                ->label('العودة للمذكرة')
////////                ->icon('heroicon-o-arrow-left')
////////                ->url(fn () => BillResource::getUrl('edit', ['record' => $this->record->id]))
////////                ->color('gray'),
////////
////////            Actions\Action::make('add_item')
////////                ->label('➕ إضافة صنف جديد')
////////                ->icon('heroicon-o-plus')
////////                ->color('primary')
////////                ->modalHeading('إضافة صنف إلى المذكرة')
////////                ->modalSubmitActionLabel('إضافة')
////////                ->modalCancelActionLabel('إلغاء')
////////                ->form([
////////                    Forms\Components\Grid::make(3)
////////                        ->schema([
////////                            Forms\Components\Select::make('item_id')
////////                                ->label('الصنف')
////////                                ->options(fn () => Item::active()->pluck('name', 'id'))
////////                                ->searchable()
////////                                ->preload()
////////                                ->required()
////////                                ->live()
////////                                ->columnSpan(2)
////////                                ->afterStateUpdated(function ($state, callable $set) {
////////                                    if ($item = Item::find($state)) {
////////                                        $set('unit_price', $item->sale_price);
////////                                    }
////////                                }),
////////
////////                            Forms\Components\TextInput::make('item_code')
////////                                ->label('كود الصنف')
////////                                ->disabled()
////////                                ->dehydrated(false),
////////                        ]),
////////
////////                    Forms\Components\Grid::make(4)
////////                        ->schema([
////////                            Forms\Components\TextInput::make('quantity')
////////                                ->label('الكمية')
////////                                ->numeric()
////////                                ->required()
////////                                ->minValue(0.01)
////////                                ->step(0.01)
////////                                ->live()
////////                                ->columnSpan(1)
////////                                ->afterStateUpdated(function ($state, callable $set, $get) {
////////                                    $unitPrice = $get('unit_price') ?? 0;
////////                                    $quantity = $state ?? 0;
////////                                    $set('total_price', $unitPrice * $quantity);
////////                                }),
////////
////////                            Forms\Components\TextInput::make('unit_price')
////////                                ->label('سعر الوحدة')
////////                                ->numeric()
////////                                ->required()
////////                                ->minValue(0)
////////                                ->step(0.01)
////////                                ->live()
////////                                ->columnSpan(1)
////////                                ->afterStateUpdated(function ($state, callable $set, $get) {
////////                                    $quantity = $get('quantity') ?? 0;
////////                                    $set('total_price', $state * $quantity);
////////                                }),
////////
////////                            Forms\Components\TextInput::make('total_price')
////////                                ->label('الإجمالي')
////////                                ->numeric()
////////                                ->disabled()
////////                                ->dehydrated(false)
////////                                ->columnSpan(1),
////////
////////                            Forms\Components\TextInput::make('batch_number')
////////                                ->label('رقم الدفعة')
////////                                ->placeholder('اختياري')
////////                                ->columnSpan(1),
////////                        ]),
////////
////////                    Forms\Components\Select::make('warehouse_id')
////////                        ->label('المستودع')
////////                        ->options(fn () => Warehouse::active()->pluck('name', 'id'))
////////                        ->searchable()
////////                        ->preload()
////////                        ->nullable()
////////                        ->visible(fn () => $this->record->type === \App\Enums\BillType::TRANSFER->value),
////////
////////                    Forms\Components\Textarea::make('notes')
////////                        ->label('ملاحظات على الصنف')
////////                        ->placeholder('ملاحظات خاصة بهذا الصنف...')
////////                        ->rows(2)
////////                        ->columnSpanFull(),
////////                ])
////////                ->action(function (array $data) {
////////                    DB::transaction(function () use ($data) {
////////
////////                        BillRecord::create([
////////                            'bill_id' => $this->record->id,
////////                            'item_id' => $data['item_id'],
////////                            'warehouse_id' => $data['warehouse_id'] ?? null,
////////                            'quantity' => $data['quantity'],
////////                            'unit_price' => $data['unit_price'],
////////                            'batch_number' => $data['batch_number'] ?? null,
////////                            'notes' => $data['notes'] ?? null,
////////                        ]);
////////
////////
////////                        $this->updateBillTotals();
////////
////////                        Notification::make()
////////                            ->title('تمت إضافة الصنف بنجاح')
////////                            ->success()
////////                            ->send();
////////                    });
////////                }),
////////        ];
////////    }
////////
////////    public function deleteItem($itemId)
////////    {
////////        DB::transaction(function () use ($itemId) {
////////            BillRecord::where('id', $itemId)->delete();
////////
////////            $this->updateBillTotals();
////////
////////            Notification::make()
////////                ->title('تم حذف الصنف بنجاح')
////////                ->success()
////////                ->send();
////////        });
////////    }
////////
////////    private function updateBillTotals(): void
////////    {
////////        $subtotal = $this->record->billRecords()->sum(DB::raw('quantity * unit_price'));
////////
////////        $this->record->update([
////////            'subtotal' => $subtotal,
////////            'total' => $subtotal - ($this->record->discount ?? 0) + ($this->record->tax ?? 0),
////////        ]);
////////
////////        $this->record->refresh();
////////    }
////////
////////    public function getBillItems()
////////    {
////////        return $this->record->billRecords()->with('item')->get();
////////    }
////////
////////    public function getTotal()
////////    {
////////        return $this->record->total;
////////    }
////////
////////    public function getSubtotal()
////////    {
////////        return $this->record->subtotal;
////////    }
////////}
//////
//////
//////namespace App\Filament\Resources\BillResource\Pages;
//////
//////use App\Filament\Resources\BillResource;
//////use App\Models\Bill;
//////use Filament\Actions\Action;
//////use Filament\Resources\Pages\ManageRecords;
//////use Filament\Tables\Columns\TextColumn;
//////use Filament\Tables\Table;
//////
//////class ManageBillItems extends ManageRecords
//////{
//////    protected static string $resource = BillResource::class;
//////    protected static string $relationship = 'billRecords'; // هذا الاسم يجب يطابق الموجود في getRelations
//////
//////    // لتخصيص الجدول عند التعديل
//////    public function table(Table $table): Table
//////    {
//////        return $table
//////            ->reorderable(false) // تعطيل إعادة الترتيب للحفاظ على ترتيب الفاتورة
//////            ->columns([
//////                TextColumn::make('item.code')
//////                    ->label('كود المادة')
//////                    ->searchable()
//////                    ->sortable(),
//////
//////               TextColumn::make('item.name')
//////                    ->label('اسم المادة')
//////                    ->searchable()
//////                    ->sortable(),
//////
//////                 TextColumn::make('warehouse.name')
//////                    ->label('المستودع')
//////                    ->searchable()
//////                    ->sortable(),
//////
//////                TextColumn::make('unit')
//////                    ->label('الوحدة')
//////                    ->searchable(),
//////
//////               TextColumn::make('quantity')
//////                    ->label('الكمية')
//////                    ->numeric()
//////                    ->sortable(),
//////
//////                 TextColumn::make('unit_price')
//////                    ->label('السعر')
//////                    ->money('SDG')
//////                    ->sortable(),
//////
//////                TextColumn::make('total_price')
//////                    ->label('القيمة')
//////                    ->money('SDG')
//////                    ->sortable(),
//////            ])
//////            ->headerActions([
//////                \Filament\Tables\Actions\Action::make('back')
//////                    ->label('عودة للفاتورة')
//////                    ->url(fn($record) => BillResource::getUrl('view', ['record' => $record]))
//////                    ->color('gray'),
//////            ]);
//////    }
//////}
////
////
////namespace App\Filament\Resources\BillResource\Pages;
////
////use App\Filament\Resources\BillResource;
////use Filament\Resources\Pages\ManageRecords;
////use Filament\Tables\Columns\TextColumn;
////use Filament\Tables\Table;
////
////class ManageBillItems extends ManageRecords
////{
////    protected static string $resource = BillResource::class;
////    protected static string $relationship = 'billRecords';
////    protected static ?string $title = 'المواد المطلوبة';
////    protected static ?string $modelLabel = 'مادة';
////    protected static ?string $pluralModelLabel = 'المواد';
////    protected static ?string $relationshipTitle = 'إدارة مواد الفاتورة';
////
////
////    public function table(Table $table): Table
////    {
////        return $table
////             ->modifyQueryUsing(fn($query) => $query->orderBy('id'))
////            ->columns([
////             TextColumn::make('item.code')
////                    ->label('كود المادة')
////                    ->searchable()
////                    ->sortable(),
////
////                TextColumn::make('item.name')
////                    ->label('اسم المادة')
////                    ->searchable()
////                    ->sortable(),
////
////              TextColumn::make('warehouse.name')
////                    ->label('المستودع')
////                    ->searchable()
////                    ->sortable(),
////
////                 TextColumn::make('unit')
////                    ->label('الوحدة')
////                    ->searchable(),
////
////               TextColumn::make('quantity')
////                    ->label('الكمية')
////                    ->numeric(decimalPlaces: 2)
////                    ->sortable()
////                    ->searchable(),
////
////              TextColumn::make('unit_price')
//////                    ->parentLabel('سعر الوحدة')
////                    ->label('السعر')
////                    ->money('SDG')
////                    ->sortable(),
////
////              TextColumn::make('total_price')
////                    ->label('الإجمالي')
////                    ->money('manage_items')
////                    ->money('SDG')
////                    ->sortable(),
////            ])
////
////            ->headerActions([
////                \Filament\Tables\Actions\ViewAction::make()
////                    ->label('عودة للفاتورة')
////                    ->url(fn() => BillResource::getUrl('view', ['record' => $this->ownerRecord])) // <--- التغيير هنا
////                    ->color('gray'),
////            ])
////
////            ->deferLoading();
////    }
////}
//
//
//namespace App\Filament\Resources\BillResource\Pages;
//
//use App\Filament\Resources\BillResource;
//use App\Models\Bill;
//use Filament\Resources\Pages\ManageRecords;
//use Filament\Tables\Columns\TextColumn;
//use Filament\Tables\Table;
//
//
//
//
//class ManageBillItems extends ManageRecords
//{
//    protected static string $resource = BillResource::class;
//    protected static string $relationship = 'billRecords';
//    protected static ?string $title = 'المواد المطلوبة';
//    protected static ?string $modelLabel = 'مادة';
//    protected static ?string $pluralModelLabel = 'المواد';
//
//    public function table(Table $table): Table
//    {
//        return $table
//            ->modifyQueryUsing(fn($query) => $query->orderBy('id'))
//            ->columns([
//                TextColumn::make('item.code')
//                    ->label('كود المادة')
//                    ->searchable(),
////                    ->sortable(),
//
//                TextColumn::make('item.name')
//                    ->label('اسم المادة')
//                    ->searchable(),
////                    ->sortable(),
//
//                TextColumn::make('warehouse.name')
//                    ->label('المستودع')
//                    ->searchable(),
////                    ->sortable(),
//
//                TextColumn::make('item.unit')
//                    ->label('الوحدة')
//                    ->searchable(),
//
//                TextColumn::make('quantity')
//                    ->label('الكمية')
//                    ->numeric(decimalPlaces: 2)
////                    ->sortable()
//                    ->searchable(),
//
//                TextColumn::make('unit_price')
//                    ->label('السعر')
//                    ->money('SDG'),
////                    ->sortable(),
//
//                TextColumn::make('total_price')
//                    ->label('الإجمالي')
//                    ->money('SDG'),
////                    ->sortable(),
//            ]);
//    }
//}


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
