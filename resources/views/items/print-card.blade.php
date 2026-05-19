<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>بطاقة مادة - {{ $item->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Tahoma', 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            padding: 10px;
            direction: rtl;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }

     
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .header-right {
            flex: 1;
            text-align: right;
            padding-right: 5px;
        }
        .header-right .line1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .header-right .line2 {
            font-size: 14px;
            margin-bottom: 20px;
        }
        .header-right .line3 {
            font-size: 12px;
        }
        .header-center {
            flex: 0 0 220px;
            text-align: center;
            padding-top: 5px;
        }
        .header-center .card-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header-center .card-number {
            font-size: 32px;
            font-weight: bold;
        }
        .header-left {
            flex: 1;
            text-align: right;
            padding-left: 5px;
            font-size: 12px;
            line-height: 1.9;
        }
        .header-left strong {
            font-size: 13px;
        }

   
        .limits-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 12px;
            border: 1px solid #000;
        }
        .limits-table td {
            border: 1px solid #000;
            padding: 6px 10px;
            text-align: center;
            vertical-align: middle;
        }

 
        .movements-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
            border: 2px solid #000;
            direction: rtl;
        }
        .movements-table th, .movements-table td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
        }
        .movements-table th {
            background-color: #d3d3d3;
            font-weight: bold;
            font-size: 10px;
        }

     
        .col-num { width: 5%; }
        .col-doc-type { width: 9%; }
        .col-doc-num { width: 11%; }
        .col-doc-date { width: 10%; }
        .col-in { width: 7%; }
        .col-out { width: 7%; }
        .col-balance { width: 8%; }
        .col-move-date { width: 10%; }
        .col-party { width: 23%; }
        .col-notes { width: 10%; }

    
        .mentioned-row td {
            height: 20px;
            font-size: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 11px;
        }
        .signature {
            width: 180px;
            margin: 0 auto;
            border-top: 1px solid #000;
            padding-top: 8px;
            margin-bottom: 10px;
        }
        .print-date {
            font-size: 9px;
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
 
    <div class="header">
        <div class="header-right">
            <div class="line1">الجمهورية العربية السورية</div>
            <div class="line2">وزارة المالية</div>
            <div class="line3">ملاحظات:</div>
        </div>
        <div class="header-center">
            <div class="card-title">بطاقة مواد رقم</div>
            <div class="card-number">{{ $item->code }}</div>
        </div>
        <div class="header-left">
            <div><strong>نموذج مستودع رقم (١٥)</strong></div>
            <div><strong>اسم المادة:</strong> {{ $item->name }}</div>
            <div><strong>رمزها:</strong> {{ $item->code }}</div>
        </div>
    </div>

 
    <table class="limits-table">
        <tr>
            <td><strong>الحد الأدنى</strong></td>
            <td><strong>الحد الأقصى</strong></td>
            <td><strong>الوحدة</strong> {{ $item->unit ?? 'عدد' }}</td>
        </tr>
    </table>

 
    <table class="movements-table">
        <thead>
            <tr>
                <th rowspan="2" class="col-num">الرقم<br>المتسلل</th>
                <th colspan="6">المستند</th>
                <th colspan="1">الحركة</th>
                <th rowspan="2" class="col-party">الجهة المسلمة أو<br>المستلمة</th>
                <th rowspan="2" class="col-notes">ملاحظات</th>
            </tr>
            <tr>
                <th class="col-doc-type">نوعه</th>
                <th class="col-doc-num">رقمه</th>
                <th class="col-doc-date">تاريخه</th>
                <th class="col-in">إدخالات</th>
                <th class="col-out">إخراجات</th>
                <th class="col-balance">الرصيد</th>
                <th class="col-move-date">تاريخ الاستلام<br>أو التسليم</th>
            </tr>
        </thead>
        <tbody>
          
            <tr class="mentioned-row">
                <td></td>
                <td>مذكور</td>
                <td></td>
                <td></td>
                <td>{{ number_format($item->opening_balance ?? 0, 0) }}</td>
                <td></td>
                <td>{{ number_format($item->opening_balance ?? 0, 0) }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            @php
                $balance = $item->opening_balance ?? 0;
            @endphp

            @forelse($movements as $index => $move)
            @php
                $balance += ($move['in'] - $move['out']);
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $move['document_type'] }}</td>
                <td>{{ $move['document_number'] }}</td>
                <td>{{ $move['date'] }}</td>
                <td>{{ $move['in'] > 0 ? number_format($move['in'], 0) : '' }}</td>
                <td>{{ $move['out'] > 0 ? number_format($move['out'], 0) : '' }}</td>
                <td>{{ number_format($balance, 0) }}</td>
                <td>{{ $move['date'] }}</td>
                <td>{{ $move['party'] }}</td>
                <td>{{ $move['notes'] ?? '' }}</td>
            </tr>
            @empty
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">التوقيع</div>
        <div class="print-date">تاريخ الطباعة: {{ now()->format('H:i d/m/Y') }}</div>
    </div>
</div>
</body>
</html>