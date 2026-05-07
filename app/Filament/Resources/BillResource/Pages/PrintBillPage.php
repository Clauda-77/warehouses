<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use App\Models\Bill;
use Filament\Resources\Pages\Page;
use Filament\Support\Facades\Request;

class PrintBillPage extends Page
{
    protected static string $resource = BillResource::class;
    protected static string $view = 'bills.print-card';

    // تم حذف array $parameters من توقيع الدالة لتتوافق مع BasePage
    // نستخدم Request للحصول على معرف السجل من الرابط مباشرة
    public function getViewData(): array
    {
        $recordId = Request::route('record');

        // جلب الفاتورة مع علاقتها (المواد، المستودعات)
        $record = Bill::with([
            'billRecords.item',
            'sourceWarehouse',
            'destinationWarehouse',
            'supplier',
            'customer'
        ])->findOrFail($recordId);

        return [
            'record' => $record,
        ];
    }
}
