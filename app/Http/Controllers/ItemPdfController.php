<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\BillRecord;
use Mpdf\Mpdf;

class ItemPdfController extends Controller
{

 public function generate($id)
    {
        $item = Item::findOrFail($id);

        $transactions = BillRecord::with(['bill.supplier'])
            ->where('item_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        $movements = [];
        $balance = $item->opening_balance ?? 0;

        foreach ($transactions as $record) {
            $bill = $record->bill;
            if (!$bill) continue;

            $quantity = (float)$record->quantity;
            $in = 0;
            $out = 0;
            $party = '';
            $documentTypeAr = '';

            switch ($bill->type) {
                case 'purchase':
                    $in = $quantity;

                    $party = $bill->supplier?->name ?? $bill->party_name ?? 'مورد غير محدد';
                    $documentTypeAr = 'شراء';
                    break;
                case 'sale':
                    $out = $quantity;
                    $party = $bill->customer?->name ?? $bill->party_name ?? 'عميل غير محدد';
                    $documentTypeAr = 'بيع';
                    break;
                case 'transfer':
                    if ($bill->destination_warehouse_id) {
                        $in = $quantity;
                        $party = 'تحويل من مخزن إلى آخر';
                    } else {
                        $out = $quantity;
                        $party = 'صرف داخلي';
                    }
                    $documentTypeAr = 'تحويل';
                    break;
                default:
                    $in = $quantity;
                    $party = $bill->party_name ?? 'حركة أخرى';
                    $documentTypeAr = 'أخرى';
            }

            $balance += ($in - $out);

$movements[] = [

    'date'            => $record->created_at->format('Y-m-d'),
    'party'           => $party,
    'in'              => $in,
    'out'             => $out,
    'document_number' => $bill->bill_number,
    'document_type'   => $documentTypeAr,
    'notes'           => $bill->notes ?? $record->notes ?? '',
];

            // $movements[] = [
            //     'date'            => $record->created_at->format('Y-m-d'),
            //     'party'           => $party,
            //     'in'              => $in,
            //     'out'             => $out,
            //     'balance'         => $balance,
            //     'document_number' => $bill->bill_number,
            //     'document_type'   => $documentTypeAr,
            //     'notes'           => $bill->notes ?? $record->notes ?? '',
            // ];
        }

        $html = view('items.print-card', compact('item', 'movements'))->render();

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'default_font'  => 'arial',
            'directionality'=> 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'margin_left'   => 15,
            'margin_right'  => 15,
            'simpleTables'  => true,
            'packTableData' => true,
        ]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output('بطاقة_مادة_' . $item->code . '.pdf', 'I');
    }


    public function generate0($id)
    {

        $item = Item::with(['category'])->findOrFail($id);


        $transactions = BillRecord::with([
            'bill.supplier',
            'bill.customer',
            'bill.sourceWarehouse',
            'bill.destinationWarehouse'
        ])
        ->where('item_id', $id)
        ->orderBy('created_at', 'asc')
        ->get();
// dd($transactions->toArray());
        $movements = [];
        $balance = $item->opening_balance ?? 0;

        foreach ($transactions as $record) {
            $bill = $record->bill;
            if (!$bill) continue;

            $quantity = $record->quantity;
            $in = 0;
            $out = 0;
            $party = '';


            switch ($bill->type) {
                case 'purchase':
                    $in = $quantity;
                    $party = $bill->supplier?->name ?? 'مورد';
                    break;
                case 'sale':
                    $out = $quantity;
                    $party = $bill->customer?->name ?? 'عميل';
                    break;
                case 'transfer':
                    if ($bill->destination_warehouse_id) {
                        $in = $quantity;
                        $party = 'تحويل من ' . ($bill->sourceWarehouse?->name ?? '') . ' إلى ' . ($bill->destinationWarehouse?->name ?? '');
                    } else {
                        $out = $quantity;
                        $party = 'صرف داخلي';
                    }
                    break;
                default:
                    $in = $quantity;
                    $party = 'حركة أخرى';
            }

            $balance += ($in - $out);

            $movements[] = [
                'date'            => $record->created_at->format('Y-m-d'),
                'party'           => $party,
                'in'              => $in,
                'out'             => $out,
                'balance'         => $balance,
                'document_number' => $bill->bill_number,
                'document_type'   => $bill->type,
                'notes'           => $bill->notes ?? '',
            ];
        }


        $html = view('items.print-card', compact('item', 'movements'))->render();


    $mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font' => 'arial',
    'directionality' => 'rtl',
    'autoScriptToLang' => true,
    'autoLangToFont' => true,
    'margin_top' => 15,
    'margin_bottom' => 15,
    'margin_left' => 15,
    'margin_right' => 15,
    'useSubstitutions' => false,
    'simpleTables' => true,
    'packTableData' => true,
    'ignore_table_widths' => false,
    'img_dpi' => 96,
]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output('item_card_' . $item->code . '.pdf', 'I');
    }
}
