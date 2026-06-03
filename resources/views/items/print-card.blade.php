<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>بطاقة مادة - {{ $item->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            max-width: 200%;
        }

        body {
            font-family: 'arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            font-weight: bold;
            direction: rtl;
        }

        .title {
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: right;
        }

        .movements-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
            border: 2px solid #000;
            direction: rtl;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
            border: 2px solid #000;
            direction: rtl;
        }

        .movements-table th,
        .movements-table td {
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
            font-size: 13px;
            margin-bottom: 10px;
        }

        .print-date {
            font-size: 11px;
            color: #333;
        }

        .containerr {
            margin: 0.5%;
        }
    </style>
</head>

<body>
    <div class="containerr">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="col-5">
                        <div class="title">الجمهورية العربية السورية</div>
                        <div class="subtitle">وزارة المالية</div>
                    </th>
                    <th class="col-4">
                        <div class="card-title">بطاقة مواد رقم</div>
                        <div class="card-number" style="margin-right: 15rem;">{{ $item->code }}</div>
                    </th>
                    <th class="col-3">
                        <div><strong>نموذج مستودع رقم (١٥)</strong></div>
                        <div><strong>اسم المادة:</strong> {{ $item->name }}</div>
                        <div><strong>رمزها:</strong> {{ $item->code }}</div>
                        <div><strong>الحد الأدنى:</strong></div>
                        <div><strong>الحد الأقصى:</strong></div>
                        <div><strong> الوحدة:</strong> {{ $item->unit ?? 'عدد' }}</div>
                    </th>
                </tr>
            </thead>
        </table>
        <br />
        <table class="movements-table">
            <thead>
                <tr class="item-table-row">
                    <th rowspan="2" style="width: 10%"> الرقم المتسلسل </th>
                    <th colspan="3" style="width: 30%"> المستند</th>
                    <th colspan="3" style="width: 30%">الحركة </th>
                    <th colspan="1" rowspan="2" style="width: 10%">تاريخ الاستلام أو التسليم</th>
                    <th colspan="1" rowspan="2" style="width: 10%"> الجهة المسلمة أو المستلمة</th>
                    <th colspan="1" rowspan="2" style="width: 10%">ملاحظات</th>
                </tr>
                <tr class="item-table-row">
                    <th scope="col">نوعه </th>
                    <th class="col">رقمه</th>
                    <th class="col">تاريخه</th>
                    <th scope="col">إدخالات</th>
                    <th scope="col">إخراجات</th>
                    <th scope="col">الرصيد</th>
                </tr>
            </thead>
            <tbody>
                <tr>
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
                    <td colspan="10">لا توجد بيانات</td>
                </tr>
                @endforelse
            </tbody>
        </table> <!-- end class movements-table-->

        <div class="footer">
            <div class="signature">التوقيع</div>
            <div class="print-date">تاريخ الطباعة: {{ now()->format('H:i d/m/Y') }}</div>
        </div> <!-- end class footer-->

    </div> <!-- end class containerr-->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>