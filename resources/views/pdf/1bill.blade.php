{{-- <!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة {{ $bill->bill_number }}</title>
    <style>
        body {
            font-family: 'Amiri', sans-serif;;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
        }
        .subtitle {
            font-size: 14px;
            margin: 5px 0;
        }
        .card-info {
            border: 1px solid #000;
            padding: 10px;
            margin: 15px 0;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">الجمهورية العربية السورية</div>
        <div class="subtitle">وزارة المالية</div>
        <div class="subtitle">بطاقة مواد رقم 2001</div>
        <div class="subtitle">نموذج مستودع رقم (15)</div>
    </div>

     @php
        $item = $bill->billRecords->first()?->item;
    @endphp
    @if($item)
    <table>
        <thead>
            <tr>
                <th>اسم المادة</th>
                <th>رمزها</th>
                <th>الحد الأدنى</th>
                <th>الحد الأقصى</th>
                <th>الوحدة</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $item->name ?? '' }}</td>
                <td>{{ $item->code ?? '' }}</td>
                <td>{{ $item->min_quantity ?? '' }}</td>
                <td>{{ $item->max_quantity ?? '' }}</td>
                <td>{{ $item->unit ?? '' }}</td>
            </tr>
        </tbody>
    </table>
    @endif

   
    <table>
        <thead>
            <tr>
                <th>المؤسسة</th>
                <th>المركبة</th>
                <th>التاريخ الاستلام أو التسليم</th>
                <th>الجهة المسلمة أو المستلمة</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->billRecords as $record)
            <tr>
                <td>{{ $bill->institution ?? '' }}</td>
                <td>{{ $bill->vehicle ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($bill->date)->format('d/m/Y') }}</td>
                <td>{{ $bill->party_name }}</td>
                <td>{{ $record->notes ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                <th>الرقم</th>
                <th>رقمه</th>
                <th>تاريخ الإخراج</th>
                <th>إدخالات/إخراجات</th>
                <th>التنسيق</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->billRecords as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->item?->code ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($bill->date)->format('d/m/Y') }}</td>
                <td>{{ $record->quantity }} {{ $record->item?->unit ?? '' }}</td>
                <td>{{ $record->coordination ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Page 1 of 1
    </div>
</body>
</html> --}}
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة {{ $bill->bill_number }}</title>
    <style>
        body {
            font-family: 'tahoma', 'dejavusans', sans-serif;
            font-size: 12pt;
            margin: 1.5cm;
            direction: rtl;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .title1 {
            font-size: 18pt;
            font-weight: bold;
        }
        .title2 {
            font-size: 16pt;
            font-weight: bold;
        }
        .subtitle {
            font-size: 14pt;
            margin: 5px 0;
        }
        .material-info {
            border: 1px solid #000;
            padding: 10px;
            margin: 20px 0;
            width: 100%;
        }
        .material-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .material-info td {
            padding: 5px;
            border: none;
            vertical-align: top;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 10pt;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="title1">الجمهورية العربية السورية</div>
    <div class="title2">وزارة المالية</div>
    <div class="subtitle">بطاقة مواد رقم 2001</div>
    <div class="subtitle">نموذج مستودع رقم (15)</div>
</div>

@php
    $firstRecord = $bill->billRecords->first();
    $item = $firstRecord?->item;
@endphp

<div class="material-info">
    <table>
         <tr>
            <td style="width:20%"><strong>اسم المادة :</strong></td>
            <td style="width:30%">{{ $item?->name ?? '—' }}</td>
            <td style="width:20%"><strong>رمزها :</strong></td>
            <td style="width:30%">{{ $item?->code ?? '—' }}</td>
         </tr>
         <tr>
            <td><strong>الحد الأدنى :</strong></td>
            <td>{{ $item?->minimum_quantity ?? '—' }}</td>
            <td><strong>الحد الأقصى :</strong></td>
            <td>{{ $item?->maximum_quantity ?? '—' }}</td>
         </tr>
         <tr>
            <td><strong>الوحدة :</strong></td>
            <td colspan="3">{{ $item?->unit ?? '—' }}</td>
         </tr>
    </table>
</div>

<!-- الجدول الأول: معلومات المؤسسة والجهة -->
<table>
    <thead>
        <tr>
            <th>المؤسسة</th>
            <th>المركبة</th>
            <th>التاريخ الاستلام أو التسليم</th>
            <th>الجهة المسلمة أو المستلمة</th>
            <th>ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $bill->sourceWarehouse?->name ?? '—' }}</td>
            <td>{{ $bill->destinationWarehouse?->name ?? '—' }}</td>
            <td>{{ \Carbon\Carbon::parse($bill->date)->format('d/m/Y') }}</td>
            <td>{{ $bill->party_name ?? $bill->supplier?->name ?? $bill->customer?->name ?? '—' }}</td>
            <td>{{ $bill->notes ?? '—' }}</td>
        </tr>
    </tbody>
</table>

<!-- الجدول الثاني: تفاصيل الأصناف -->
<table>
    <thead>
        <tr>
            <th>الرقم</th>
            <th>رقم الصنف</th>
            <th>تاريخ الإخراج</th>
            <th>إدخالات / إخراجات</th>
            <th>التنسيق</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bill->billRecords as $index => $record)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $record->item?->code ?? '—' }}</td>
            <td>{{ \Carbon\Carbon::parse($bill->date)->format('d/m/Y') }}</td>
            <td>{{ $record->quantity }} {{ $record->item?->unit ?? '' }}</td>
            <td>{{ $record->notes ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Page 1 of 1
</div>

</body>
</html>

{{-- <!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة {{ $bill->bill_number }}</title>
    <style>
        body {
            font-family: 'tahoma', 'dejavusans', sans-serif;
            font-size: 12pt;
            margin: 1cm;
            direction: rtl;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .title1 {
            font-size: 18pt;
            font-weight: bold;
        }
        .title2 {
            font-size: 16pt;
            font-weight: bold;
        }
        .subtitle {
            font-size: 14pt;
            margin: 5px 0;
        }
        .material-info {
            border: 1px solid #000;
            padding: 8px;
            margin: 15px 0;
            text-align: center;
            width: 100%;
        }
        .material-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .material-info td {
            padding: 5px;
            border: none;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            font-size: 10pt;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="title1">الجمهورية العربية السورية</div>
    <div class="title2">وزارة المالية</div>
    <div class="subtitle">بطاقة مواد رقم 2001</div>
    <div class="subtitle">نموذج مستودع رقم (15)</div>
</div>

@php
    $firstRecord = $bill->billRecords->first();
    $item = $firstRecord?->item;
@endphp

<div class="material-info">
    <table>
        <tr>
            <td style="width:20%"><strong>اسم المادة :</strong></td>
            <td style="width:30%">{{ $item->name ?? '—' }}</td>
            <td style="width:20%"><strong>رمزها :</strong></td>
            <td style="width:30%">{{ $item->code ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>الحد الأدنى :</strong></td>
            <td>{{ $item->min_quantity ?? '—' }}</td>
            <td><strong>الحد الأقصى :</strong></td>
            <td>{{ $item->max_quantity ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>الوحدة :</strong></td>
            <td colspan="3">{{ $item->unit ?? '—' }}</td>
        </tr>
    </table>
</div>

<!-- الجدول الأول: معلومات المؤسسة والجهة -->
<table>
    <thead>
        <tr>
            <th>المؤسسة</th>
            <th>المركبة</th>
            <th>التاريخ الاستلام أو التسليم</th>
            <th>الجهة المسلمة أو المستلمة</th>
            <th>ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $bill->institution ?? '—' }}</td>
            <td>{{ $bill->vehicle ?? '—' }}</td>
            <td>{{ \Carbon\Carbon::parse($bill->date)->format('d/m/Y') }}</td>
            <td>{{ $bill->party_name ?? '—' }}</td>
            <td>{{ $bill->notes ?? '—' }}</td>
        </tr>
    </tbody>
</table>

<!-- الجدول الثاني: تفاصيل الأصناف (إدخالات/إخراجات) -->
<table>
    <thead>
        <tr>
            <th>الرقم</th>
            <th>رقمه</th>
            <th>تاريخ الإخراج</th>
            <th>إدخالات / إخراجات</th>
            <th>التنسيق</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bill->billRecords as $index => $record)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $record->item->code ?? '—' }}</td>
            <td>{{ \Carbon\Carbon::parse($bill->date)->format('d/m/Y') }}</td>
            <td>{{ $record->quantity }} {{ $record->item->unit ?? '' }}</td>
            <td>{{ $record->coordination ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Page 1 of 1
</div>

</body>
</html> --}}