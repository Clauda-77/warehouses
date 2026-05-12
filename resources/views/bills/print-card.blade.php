<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>بطاقة مواد {{ $bill->bill_number ?? 'غير محدد' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                margin: 0;
                size: A4;
            }

            body {
                -webkit-print-color-adjust: exact;
                overflow-x: hidden;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            max-width: 100%;
        }

        body {
            font-family: 'arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .containerr {
            margin: 0.5%;
        }

        /* .left-section {   margin: 15px 16rem 0 0;}
        .center-section { margin: 20px 10rem 0 0; }
        .right-section { margin: 15px 25px 0 0; } */
        .model-number {
            font-size: 25px;
            margin-bottom: 8px;
        }

        .item-line {
            margin: 4px 0;
            font-size: 20px;
        }

        .title {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 30px;
            font-weight: bold;
        }

        .card-number {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-right: 10px;
        }

        .notes-section {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 15px;
            min-height: 40px;
            text-align: right;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 20px;
        }

        th,
        td {
            font-weight: bold;
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
            font-size: 20px;
        }

        /* .table-first th {
            background-color: #c0c0c0; 
            background-color: #d3d3d3;
          
        } */

        .main-header {
            background-color: #c0c0c0;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
        }

        .signature-section {
            width: 150px;
            margin: 0 auto;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            height: 30px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 20px;
            margin-top: 5px;
        }

        .page-number {
            font-weight: bold;

            text-align: center;
            font-size: 15px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    @php
    $firstRecord = $bill->billRecords->first();
    $item = $firstRecord?->item;
    @endphp

    <div class="containerr">
        <div class="row">
            <div class="col-5">
                <div class="title">الجمهورية العربية السورية</div>
                <div class="subtitle" style="margin: 3px 55px 0 0 ;">وزارة المالية</div>
            </div>
            <div class="col-5" > 
                <div class="subtitle">بطاقة مواد رقم</div>
                <div class="card-number">{{ $bill->bill_number ?? '2001' }}</div>
            </div>
            <div class="col-2" >
                <div class="model-number"><strong>نموذج مستودع رقم (15)</strong></div>
                <div class="item-line"><strong>اسم المادة:</strong> {{ $item?->name ?? 'ورق أبيض A4 غراماج' }}</div>
                <div class="item-line"><strong>رمزها:</strong> {{ $item?->code ?? '1111' }}</div>
                <div style="margin-top: 8px;">
                    <div class="item-line"><strong>الحد الأدنى:</strong> {{ $item?->minimum_quantity ?? '0' }}</div>
                    <div class="item-line"><strong>الحد الأقصى:</strong> {{ $item?->maximum_quantity ?? '0' }}</div>
                    <div class="item-line"><strong>الوحدة:</strong> {{ $item?->unit ?? 'ربطة' }}</div>
                </div>
            </div>
        </div>
        <table class="table-first">
            <thead>
                <tr class="main-header">
                    <th>المؤسسة</th>
                    <th>المركبة</th>
                    <th>التاريخ الاستلام أو التسليم</th>
                    <th>الجهة المسلمة أو المستلمة</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $bill->sourceWarehouse?->name ?? 'المستودع المركزي' }}</td>
                    <td>{{ $bill->destinationWarehouse?->name ?? 'كلية الطب البشري' }}</td>
                    <td>{{ \Carbon\Carbon::parse($bill->date)->format('d/m/Y') }}</td>
                    <td>{{ $bill->party_name ?? $bill->supplier?->name ?? $bill->customer?->name ?? 'كلية الطب البشري / على علمي' }}</td>
                    <td>{{ $bill->notes ?? '—' }}</td>
                </tr>
            </tbody>
        </table>


        <table class="table-first">
            <thead>
                <tr class="main-header">
                    <th>الرقم</th>
                    <th>رقمه</th>
                    <th>تاريخ الإخراج</th>
                    <th>إدخالات</th>
                    <th>إخراجات</th>
                    <th>التنسيق</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bill->billRecords as $index => $record)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $record->item?->code ?? $record->item_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($bill->date)->format('d/m/Y') }}</td>
                    <td>{{ $record->quantity }}</td>
                    <td>0</td>
                    <td>{{ $record->quantity }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">لا توجد بيانات</td>
                </tr>
                @endforelse
            </tbody>
        </table>


        <div class="footer">
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">التوقيع</div>
            </div>
        </div>

        <div class="page-number">Page 1 of 1</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>