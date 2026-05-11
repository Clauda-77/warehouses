
    <!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>بطاقة مواد {{ $record->bill_number }}</title>
    <style>
        @media print {
            @page { margin: 0; size: A4; }
            body { -webkit-print-color-adjust: exact; }
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'tahoma', sans-serif; font-size: 11px; line-height: 1.4; }
        .container { max-width: 900px; margin: 0 auto; }

        .header-wrapper { display: flex; justify-content: space-between; margin-bottom: 20px; gap: 20px; }
        .left-section { flex: 1; border: 1px solid #000; padding: 10px; font-size: 10px; }
        .center-section { flex: 0 0 120px; text-align: center; display: flex; flex-direction: column; justify-content: center; }
        .right-section { flex: 1; text-align: right; }

        .model-number { font-size: 10px; margin-bottom: 8px; }
        .item-line { margin: 4px 0; font-size: 10px; }
        .title { font-size: 13px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 12px; font-weight: bold; margin: 3px 0; }
        .card-number { font-size: 24px; font-weight: bold; color: #000; margin: 5px 0; }

        .notes-section { border: 1px solid #000; padding: 8px; margin-bottom: 15px; min-height: 40px; text-align: right; font-size: 10px; }

        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 9px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; vertical-align: middle; }
        th { background-color: #d3d3d3; font-weight: bold; font-size: 8px; }
        .table-first th { background-color: #c0c0c0; }
        .main-header { background-color: #c0c0c0; font-weight: bold; }

        .footer { text-align: center; margin-top: 40px; }
        .signature-section { width: 150px; margin: 0 auto; text-align: center; }
        .signature-line { border-top: 1px solid #000; height: 30px; }
        .signature-label { font-weight: bold; font-size: 10px; margin-top: 5px; }
        .page-number { text-align: center; font-size: 10px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <!-- الرأس -->
    <div class="header-wrapper">
        <div class="left-section">
            <div class="model-number"><strong>نموذج مستودع رقم (10)</strong></div>
            <div class="item-line"><strong>اسم المادة:</strong> {{ $record->billRecords->first()->item->name ?? 'غير محدد' }}</div>
            <div class="item-line"><strong>رمزها:</strong> {{ $record->billRecords->first()->item->code ?? 'N/A' }}</div>
            <div style="margin-top: 8px;">
                <div class="item-line"><strong>الحد الأدنى:</strong> @if($record->billRecords->first()->item) {{ number_format($record->billRecords->first()->item->minimum_quantity) }} @endif</div>
                <div class="item-line"><strong>الحد الأقصى:</strong> —</div>
                <div class="item-line"><strong>الوحدة:</strong> {{ $record->billRecords->first()->item->unit ?? 'عدد' }}</div>
            </div>
        </div>
        <div class="center-section">
            <div class="subtitle">بطاقة مواد رقم</div>
            <div class="card-number">{{ $record->bill_number }}</div>
        </div>
        <div class="right-section">
            <div class="title">الجمهورية العربية السورية</div>
            <div class="subtitle">وزارة المالية</div>
            <div style="margin-top: 20px; font-weight: bold; font-size: 11px;">ملاحظات:</div>
        </div>
    </div>

    <!-- ملاحظات -->
    <div class="notes-section">
        <strong>ملاحظات:</strong>
        <p style="margin:0; white-space: pre-wrap;">{{ $record->notes }}</p>
    </div>



    <table class="table-first">
        <thead>
        <tr class="main-header">
            <th style="width: 20%;">الصنف</th>
            <th style="width: 25%;">الجهة المسلمة أو المستلمة</th>
            <th style="width: 20%;">تاريخ الاستلام أو التسليم</th>
            <th style="width: 35%;">المستودع / الملحق</th>
        </tr>
        </thead>
        <tbody>
        @foreach($record->billRecords as $index => $itemRecord)
            <tr>
                <td>{{ $itemRecord->item->name }}</td>
                <td>{{ $record->party_name ?? '-' }}</td>
                <td>{{ $itemRecord->created_at->format('d/m/Y') }}</td>
                <td>
                    @if($record->warehouse)
                        {{ $record->warehouse->name }}
                    @else
                        عام
                    @endif
                </td>
            </tr>
        @endforeach
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

</body>
</html>
