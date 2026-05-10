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


    public function getViewData(): array
    {
        $recordId = Request::route('record');


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
