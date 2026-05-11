<?php


namespace App\Http\Controllers;

use App\Models\Bill;
use Mpdf\Mpdf;

class BillPdfController extends Controller
{    public function generate($id)
{
    $bill = Bill::with(['billRecords.item'])->findOrFail($id);


    return view('bills.print-card', ['bill' => $bill]);
}
    public function generate2($id)
    {
        $bill = Bill::with([
            'billRecords.item',
            'sourceWarehouse',
            'destinationWarehouse',
            'supplier',
            'customer',
        ])->findOrFail($id);

        $html = view('bills.print-card', ['bill' => $bill])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'tahoma',
            'directionality' => 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('warehouse_card_' . $bill->bill_number . '.pdf', 'I');
    }
}
//
//
//
//
//namespace App\Http\Controllers;
//
//use App\Models\Bill;
//use App\Models\Item;
//use Mpdf\Mpdf;
//
//class BillPdfController extends Controller
//{
//    public function generate($id)
//    {
//        $bill = Bill::with(['billRecords.item', 'sourceWarehouse', 'destinationWarehouse', 'supplier', 'customer'])->findOrFail($id);
//
//        $html = view('pdf.bill', compact('bill'))->render();
//
//        $mpdf = new Mpdf([
//            'mode' => 'utf-8',
//            'format' => 'A4',
//            'default_font' => 'tahoma',
//            'directionality' => 'rtl',
//            'autoScriptToLang' => true,
//            'autoLangToFont' => true,
//        ]);
//
//        $mpdf->WriteHTML($html);
//        return $mpdf->Output('warehouse_card_' . $bill->bill_number . '.pdf', 'I');
//    }
//    public function generate0($id)
//    {
//        $bill = Bill::with(['billRecords.item', 'sourceWarehouse', 'destinationWarehouse', 'supplier', 'customer'])->findOrFail($id);
//        $html = view('pdf.bill', compact('bill'))->render();
//
//        $mpdf = new Mpdf([
//            'mode' => 'utf-8',
//            'format' => 'A4',
//            'default_font' => 'tahoma',
//            'directionality' => 'rtl',
//            'autoScriptToLang' => true,
//            'autoLangToFont' => true,
//        ]);
//        $mpdf->WriteHTML($html);
//        return $mpdf->Output('invoice.pdf', 'I');
//    }
//    public function generatee($id)
//    {
//        $bill = Bill::with('billRecords.item')->findOrFail($id);
//        $html = view('pdf.bill', compact('bill'))->render();
//
//        $mpdf = new Mpdf([
//            'mode' => 'utf-8',
//            'format' => 'A4',
//            'default_font' => 'tahoma',
//            'directionality' => 'rtl',
//            'autoScriptToLang' => true,
//            'autoLangToFont' => true,
//        ]);
//        $mpdf->WriteHTML($html);
//        return $mpdf->Output('invoice.pdf', 'I');
//    }
//}
