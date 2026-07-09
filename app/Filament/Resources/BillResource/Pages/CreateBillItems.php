<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\Bill;
use App\Models\Item;
use App\Models\BillRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use App\Enums\BillType;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CreateBillItems extends Page
{
    protected static string $resource = BillResource::class;
    protected static string $view = 'filament.resources.bill-resource.pages.create-bill-items';

    public Bill $bill;
    public array $items = [];
    public int $itemCount = 0;

    public function mount($bill): void
    {
        $this->bill = Bill::findOrFail($bill);


        $this->addItem();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('إضافة مواد للفاتورة')
                ->description("فاتورة رقم: {$this->bill->bill_number}")
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('المواد')
                        ->schema([
                            Forms\Components\Grid::make(8)
                                ->schema([
                                    Forms\Components\Select::make('item_id')
                                        ->label('المادة')
                                        ->options(Item::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state && $item = Item::find($state)) {
                                                $set('code', $item->code);
                                                $set('unit', $item->unit);
                                                $set('unit_price', $item->sale_price);
                                                $set('batch_number', 'BATCH-' . date('Ymd'));
                                            }
                                        })
                                        ->columnSpan(3),

                                    Forms\Components\TextInput::make('item_code')
                                        ->label('كود المادة')
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('quantity')
                                        ->label('الكمية')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0.01)
                                        ->step(0.01)
                                        ->default(1)
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('unit')
                                        ->label('الوحدة')
                                        ->disabled()
                                        ->dehydrated()
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('unit_price')
                                        ->label('سعر الوحدة')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0)
                                        ->columnSpan(1),

                                    Forms\Components\TextInput::make('total_price')
                                        ->label('القيمة')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated()
                                        ->default(0)
                                        ->columnSpan(1),
                                ])
                                ->columnSpanFull(),
                        ])
                        ->defaultItems(1)
                        ->createItemButtonLabel('إضافة مادة أخرى')
                        ->reorderable(false)
                        ->columnSpanFull(),
                ]),
        ];
    }

    public function addItem(): void
    {
        $this->itemCount++;
        $this->items[] = [
            'item_id' => null,
            'quantity' => 1,
            'unit_price' => 0,
            'total_price' => 0,
        ];
    }

    public function removeItem($index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->itemCount--;
    }

    public function saveItems(): void
    {
        $data = $this->form->getState();

        if (isset($data['items'])) {
            foreach ($data['items'] as $itemData) {
                if (!empty($itemData['item_id'])) {

                    $totalPrice = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);

                    BillRecord::create([
                        'bill_id' => $this->bill->id,
                        'item_id' => $itemData['item_id'],
                        'warehouse_id' => $this->getWarehouseId(),
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'total_price' => $totalPrice,
                        'cost_price' => Item::find($itemData['item_id'])->purchase_price ?? 0,
                        'batch_number' => $itemData['batch_number'] ?? 'BATCH-' . date('Ymd'),
                        'stock_before' => $this->getStockBefore($itemData['item_id']),
                        'stock_after' => $this->calculateStockAfter($itemData['item_id'], $itemData['quantity']),
                    ]);
                }
            }


            $this->updateBillTotals();

            Notification::make()
                ->title('تم إضافة المواد بنجاح')
                ->success()
                ->send();


            $this->redirect(BillResource::getUrl('edit', ['record' => $this->bill->id]));
        }
    }

    private function getWarehouseId(): ?int
    {
        return match($this->bill->type) {
            BillType::PURCHASE->value, BillType::RETURN->value => $this->bill->destination_warehouse_id,
            BillType::TRANSFER->value, BillType::ADJUSTMENT->value => $this->bill->source_warehouse_id,
            default => null,
        };
    }

    private function getStockBefore($itemId): float
    {

        return 0;
    }

    private function calculateStockAfter($itemId, $quantity): float
    {
        $stockBefore = $this->getStockBefore($itemId);

        return match($this->bill->type) {
            BillType::PURCHASE->value, BillType::RETURN->value => $stockBefore + $quantity,
            BillType::TRANSFER->value, BillType::ADJUSTMENT->value => $stockBefore - $quantity,
            default => $stockBefore,
        };
    }

    private function updateBillTotals(): void
    {
        $subtotal = BillRecord::where('bill_id', $this->bill->id)->sum('total_price');

        $this->bill->update([
            'subtotal' => $subtotal,
            'total' => $subtotal - $this->bill->discount + $this->bill->tax,
        ]);
    }

    protected function getActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ المواد')
                ->action('saveItems')
                ->color('primary')
                ->icon('heroicon-o-check'),

            Action::make('cancel')
                ->label('إلغاء')
                ->url(BillResource::getUrl('edit', ['record' => $this->bill->id]))
                ->color('gray')
                ->icon('heroicon-o-x-mark'),
        ];
    }
}
