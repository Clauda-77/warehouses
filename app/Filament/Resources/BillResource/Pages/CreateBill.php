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

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

    protected function getRedirectUrl(): string
    {
        return BillResource::getUrl('edit', ['record' => $this->record->id]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المرحلة 1: معلومات المذكرة الأساسية')
                    ->description('أدخل المعلومات الأساسية للمذكرة أولاً')
                    ->icon('heroicon-o-document')
                    ->schema([

                         Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->label('تاريخ المذكرة')
                                    ->required()
                                    ->default(now())
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('bill_number')
                                    ->label('رقم المذكرة (المتسلسل)')
                                    ->required()
                                    ->unique(Bill::class, 'bill_number', ignoreRecord: true)
                                    ->default(fn () => 'MEMO-' . (Bill::withTrashed()->count() + 1))
                                    ->columnSpan(1),
                            ]),

                         Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('reference_number')
                                    ->label('رقم المجلد')
                                    ->placeholder('أدخل رقم المجلد')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('financial_number')
                                    ->label('رقم المذكرة المالي')
                                    ->placeholder('أدخل رقم المذكرة المالي')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->columnSpan(1),
                            ]),

                         Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('invoice_number')
                                    ->label('رقم الفاتورة')
                                    ->placeholder('أدخل رقم الفاتورة')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('invoice_date')
                                    ->label('تاريخ الفاتورة')
                                    ->displayFormat('d/m/Y')
                                    ->nullable()
                                    ->columnSpan(1),
                            ]),

                         Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('purchase_order_number')
                                    ->label('رقم الطلب')
                                    ->placeholder('أدخل رقم الطلب')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('purchase_order_date')
                                    ->label('تاريخ الطلب')
                                    ->displayFormat('d/m/Y')
                                    ->nullable()
                                    ->columnSpan(1),
                            ]),

                         Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('supplier_id')
                                    ->label('اسم المورد')
                                    ->relationship('supplier', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->createOptionForm([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('code')
                                                    ->label('كود المورد')
                                                    ->required()
                                                    ->unique(\App\Models\Supplier::class, 'code')
                                                    ->maxLength(255)
                                                    ->default(fn () => 'SUP-' . (\App\Models\Supplier::count() + 1))
                                                    ->columnSpan(1),

                                                Forms\Components\TextInput::make('name')
                                                    ->label('اسم المورد')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('contact_person')
                                                    ->label('جهة الاتصال')
                                                    ->maxLength(255)
                                                    ->columnSpan(1),

                                                Forms\Components\TextInput::make('phone')
                                                    ->label('الهاتف')
                                                    ->tel()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('email')
                                                    ->label('البريد الإلكتروني')
                                                    ->email()
                                                    ->maxLength(255)
                                                    ->columnSpan(1),

                                                Forms\Components\TextInput::make('address')
                                                    ->label('العنوان')
                                                    ->maxLength(255)
                                                    ->columnSpan(1),
                                            ]),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('نشط')
                                            ->default(true)
                                            ->columnSpanFull(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return \App\Models\Supplier::create([
                                            'code' => $data['code'],
                                            'name' => $data['name'],
                                            'contact_person' => $data['contact_person'] ?? null,
                                            'phone' => $data['phone'] ?? null,
                                            'email' => $data['email'] ?? null,
                                            'address' => $data['address'] ?? null,
                                            'is_active' => $data['is_active'] ?? true,
                                        ])->id;
                                    })
                                    ->createOptionAction(fn ($action) => $action->modalHeading('إضافة مورد جديد'))
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('supplier_number')
                                    ->label('رقم المورد')
                                    ->placeholder('أدخل رقم المورد')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->columnSpan(1),
                            ]),

                         Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('source_warehouse_id')
                                    ->label('المستودع المصدر')
                                    ->options(fn () => Warehouse::active()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->columnSpan(1),

                                Forms\Components\Select::make('destination_warehouse_id')
                                    ->label('المستودع الوجهة')
                                    ->options(fn () => Warehouse::active()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->columnSpan(1),
                            ]),

                         Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\TextInput::make('party_name')
                                    ->label('جهة الإرسال (نص حر)')
                                    ->placeholder('أدخل اسم الجهة المرسلة')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->columnSpanFull(),
                            ]),

                         Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->label('ملاحظات')
                                    ->placeholder('أي ملاحظات إضافية حول المذكرة...')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columns(1),

                 Forms\Components\Hidden::make('type')
                    ->default(BillType::PURCHASE->value),

                Forms\Components\Hidden::make('status')
                    ->default(BillStatus::DRAFT->value),

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
                    ->url(BillResource::getUrl('edit', ['record' => $this->record->id]))
                    ->button(),
            ]);
    }
}
 