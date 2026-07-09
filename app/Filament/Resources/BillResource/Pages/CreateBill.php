<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\Bill;
use App\Models\Warehouse;
use App\Enums\BillType;
use App\Enums\BillStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

// protected function getRedirectUrl(): string
//{
//    return BillResource::getUrl('items', ['record' => $this->record->id]);
//}
//
//protected function afterCreate(): void
//{
//     $this->redirect(BillResource::getUrl('items', ['record' => $this->record->id]));
//}
public function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('المرحلة 1: معلومات المذكرة الأساسية')
                ->description('أدخل المعلومات الأساسية للمذكرة أولاً')
                ->icon('heroicon-o-document')
                ->schema([

                    Forms\Components\Grid::make(4)
                        ->schema([
                            Forms\Components\TextInput::make('bill_number')
                                ->label('رقم المذكرة')
                                ->required()
                                ->unique(Bill::class, 'bill_number')
                                ->default(fn () => 'MEMO-' . (Bill::count() + 1))
                                ->columnSpan(1),

                            Forms\Components\DatePicker::make('date')
                                ->label('تاريخ المذكرة')
                                ->required()
                                ->default(now())
                                ->displayFormat('d/m/Y')
                                ->columnSpan(1),

                            Forms\Components\Select::make('type')
                                ->label('نوع المذكرة')
                                ->required()
                                ->options([
                                    BillType::PURCHASE->value => 'شراء',
                                    BillType::TRANSFER->value => 'تحويل',
                                    BillType::ADJUSTMENT->value => 'تعديل',
                                    BillType::RETURN->value => 'مرتجع',
                                ])
                                ->default(BillType::PURCHASE->value)
                                ->live()
                                ->columnSpan(1),

                            Forms\Components\Select::make('status')
                                ->label('حالة المذكرة')
                                ->required()
                                ->options([
                                    BillStatus::DRAFT->value => 'مسودة',
                                    'pending' => 'معلقة',
                                    BillStatus::COMPLETED->value => 'مكتملة',
                                    'cancelled' => 'ملغاة',
                                ])
                                ->default(BillStatus::DRAFT->value)
                                ->columnSpan(1),
                        ]),


                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Select::make('supplier_id')
                                ->label('المورد')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->visible(fn ($get) => in_array($get('type'), [
                                    BillType::PURCHASE->value,
                                    BillType::RETURN->value
                                ]))
                                ->columnSpan(1),

                            Forms\Components\Select::make('source_warehouse_id')
                                ->label('المستودع المصدر')
                                ->options(fn () => Warehouse::active()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->visible(fn ($get) => in_array($get('type'), [
                                    BillType::TRANSFER->value,
                                    BillType::ADJUSTMENT->value,
                                    BillType::RETURN->value,
                                ]))
                                ->columnSpan(1),

                            Forms\Components\Select::make('destination_warehouse_id')
                                ->label('المستودع الوجهة')
                                ->options(fn () => Warehouse::active()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->visible(fn ($get) => in_array($get('type'), [
                                    BillType::PURCHASE->value,
                                    BillType::TRANSFER->value,
                                    BillType::ADJUSTMENT->value,
                                    BillType::RETURN->value,
                                ]))
                                ->columnSpan(1),
                        ]),


                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('reference_number')
                                ->label('رقم المرجع')
                                ->placeholder('رقم المرجع')
                                ->maxLength(255)
                                ->nullable()
                                ->columnSpan(1),

                            Forms\Components\DatePicker::make('reference_date')
                                ->label('تاريخ المرجع')
                                ->displayFormat('d/m/Y')
                                ->nullable()
                                ->columnSpan(1),
                        ]),


                    Forms\Components\Textarea::make('notes')
                        ->label('ملاحظات')
                        ->placeholder('أي ملاحظات إضافية حول المذكرة...')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(1),

            Forms\Components\Hidden::make('created_by')
                ->default(filament()->auth()->id()),

            Forms\Components\Hidden::make('subtotal')
                ->default(0),

            Forms\Components\Hidden::make('discount')
                ->default(0),

            Forms\Components\Hidden::make('tax')
                ->default(0),

            Forms\Components\Hidden::make('total')
                ->default(0),
        ]);
}



    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تم حفظ معلومات المذكرة بنجاح';
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
         ->title('تم حفظ معلومات المذكرة')
        ->body('الآن يمكنك إضافة المواد للمذكرة')
        ->actions([
            \Filament\Notifications\Actions\Action::make('view')
                ->label('الانتقال لإضافة المواد')
                ->url(BillResource::getUrl('items', ['record' => $this->record->id]))
                ->button(),
        ]);
    }
}
