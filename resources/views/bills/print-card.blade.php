{{--<!DOCTYPE html>--}}
{{--<html dir="rtl" lang="ar">--}}

{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <title>بطاقة المذكرة {{ $bill->bill_number ?? 'غير محدد' }}</title>--}}
{{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">--}}
{{--    <style>--}}
{{--        @media print {--}}
{{--            @page {--}}
{{--                margin: 0;--}}
{{--                size: A4;--}}
{{--            }--}}

{{--            body {--}}
{{--                -webkit-print-color-adjust: exact;--}}
{{--                overflow-x: hidden;--}}
{{--            }--}}
{{--        }--}}

{{--        * {--}}
{{--            margin: 0;--}}
{{--            padding: 0;--}}
{{--            box-sizing: border-box;--}}
{{--            max-width: 100%;--}}
{{--        }--}}

{{--        body {--}}
{{--            font-family: 'arial', sans-serif;--}}
{{--            font-size: 11px;--}}
{{--            line-height: 1.4;--}}
{{--        }--}}

{{--        .containerr {--}}
{{--            margin: 0.5%;--}}
{{--        }--}}

{{--        .model-number {--}}
{{--            font-size: 25px;--}}
{{--            margin-bottom: 8px;--}}
{{--        }--}}

{{--        .item-line {--}}
{{--            margin: 4px 0;--}}
{{--            font-size: 20px;--}}
{{--        }--}}

{{--        .title {--}}
{{--            font-size: 30px;--}}
{{--            font-weight: bold;--}}
{{--            margin-bottom: 5px;--}}
{{--        }--}}

{{--        .subtitle {--}}
{{--            font-size: 30px;--}}
{{--            font-weight: bold;--}}
{{--            text-align: right;--}}
{{--        }--}}

{{--        .faculty-name {--}}
{{--            font-size: 24px;--}}
{{--            font-weight: bold;--}}
{{--            color: #000;--}}
{{--            text-align: right;--}}
{{--            /* margin-right: -5px; */--}}
{{--        }--}}

{{--        .notes-section {--}}
{{--            border: 1px solid #000;--}}
{{--            padding: 8px;--}}
{{--            margin-bottom: 15px;--}}
{{--            min-height: 40px;--}}
{{--            text-align: right;--}}
{{--            font-size: 10px;--}}
{{--        }--}}

{{--        table {--}}
{{--            width: 100%;--}}
{{--            border-collapse: collapse;--}}
{{--            margin: 15px 0;--}}
{{--            font-size: 20px;--}}
{{--        }--}}

{{--        th,--}}
{{--        td,--}}
{{--        tr {--}}
{{--            font-weight: bold;--}}
{{--            border: 1px solid #000;--}}
{{--            padding: 5px;--}}
{{--            text-align: center;--}}
{{--            vertical-align: middle;--}}
{{--            font-size: 20px;--}}
{{--        }--}}

{{--        .footer {--}}
{{--            text-align: center;--}}
{{--            margin-top: 40px;--}}
{{--        }--}}

{{--        .signature-section {--}}
{{--            width: 150px;--}}
{{--            margin: 0 auto;--}}
{{--            text-align: center;--}}
{{--        }--}}

{{--        .signature-line {--}}
{{--            border-top: 1px solid #000;--}}
{{--            height: 30px;--}}
{{--        }--}}

{{--        .signature-label {--}}
{{--            font-weight: bold;--}}
{{--            font-size: 20px;--}}
{{--            margin-top: 5px;--}}
{{--        }--}}

{{--        .page-number {--}}
{{--            font-weight: bold;--}}

{{--            text-align: center;--}}
{{--            font-size: 15px;--}}
{{--            margin-top: 10px;--}}
{{--        }--}}

{{--        .note-subject1 {--}}
{{--            font-weight: bold;--}}
{{--            text-align: center;--}}
{{--            font-size: 25px;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}

{{--<body>--}}

{{--    @php--}}
{{--    $firstRecord = $bill->billRecords->first();--}}
{{--    $item = $firstRecord?->item;--}}
{{--    @endphp--}}

{{--    <div class="containerr">--}}
{{--        <div class="row">--}}
{{--            <div class="col-5">--}}
{{--                <div class="title">الجمهورية العربية السورية</div>--}}
{{--                <div class="subtitle" style="margin: 3px 55px 0 0 ;">وزارة المالية</div>--}}
{{--            </div> <!-- end class col-5 -->--}}
{{--            <div class="col-4">--}}
{{--                <!-- <div class="subtitle"> مذكرة {{ $bill ->type }} </div> -->--}}
{{--                <div class="subtitle"> مذكرة استلام </div>--}}
{{--                <div class="faculty-name">{{ $bill->destinationWarehouse ?? 'كلية الطب البشري'}}</div>--}}
{{--            </div> <!-- end class col-5 -->--}}
{{--            <div class="col-3">--}}
{{--                <div class="model-number"><strong>نموذج مستودع رقم (15)</strong></div>--}}
{{--                <div class="item-line"><strong>رقم المجلد :</strong> {{ $bill?->reference_number ?? 'ورق أبيض A4 غراماج' }}</div>--}}
{{--                <div class="item-line"><strong>تاريخ المذكرة:</strong> {{ $bill && $bill->reference_date ? $bill->reference_date->format('d/m/Y') : '1111' }}</div>--}}
{{--                <div style="margin-top: 8px;">--}}
{{--                    <div class="item-line"><strong>رقم :</strong> {{ $bill?->bill_number ?? '0' }}</div>--}}
{{--                </div>--}}
{{--            </div> <!-- end class col-2 -->--}}
{{--        </div> <!-- end class row -->--}}
{{--        <br />--}}
{{--        <div class="note">--}}
{{--            <p class="note-subject1">--}}
{{--                إن المواد المذكورة أدناه وردت من مديرية الكتب بموجب الفاتورة رقم--}}
{{--                {{ $bill?->bill_number ?? '0' }}--}}
{{--                تاريخ {{ $bill && $bill->reference_date ? $bill->reference_date->format('d/m/Y') : '---' }}--}}
{{--            </p> <!-- end class note-subject1 -->--}}
{{--        </div> <!-- end class note -->--}}

{{--        <table class="table table-bordered table-hover">--}}
{{--            <thead>--}}
{{--                <tr class="table-active">--}}
{{--                    <th colspan="5"> المادة </th>--}}
{{--                    <th scope="col" rowspan="2">الكمية المستلمة</th>--}}
{{--                    <th scope="col" rowspan="2">السعر</th>--}}
{{--                    <th scope="col" rowspan="2">القيمة</th>--}}
{{--                    <th scope="col" rowspan="2">رقم البطاقة</th>--}}
{{--                    <th scope="col" rowspan="2">ملاحظات</th>--}}
{{--                </tr>--}}
{{--                <tr class="table-active">--}}
{{--                    <th scope="col">الرقم المتسلسل</th>--}}
{{--                    <th scope="col">رمزها</th>--}}
{{--                    <th scope="col">اسمها</th>--}}
{{--                    <th scope="col">أوصافها</th>--}}
{{--                    <th scope="col">وحدتها</th>--}}
{{--                </tr>--}}
{{--            </thead>--}}
{{--            <tbody>--}}
{{--                @forelse($bill->billRecords as $index => $record)--}}
{{--                <tr>--}}
{{--                    <td>{{ $index + 1 }}</td>--}}
{{--                    <td>{{ $item->code }}</td>--}}
{{--                    <td>{{ $item->name }}</td>--}}
{{--                    <td> ---- </td>--}}
{{--                    <td>{{ $item->unit }}</td>--}}
{{--                    <td> ---- </td>--}}
{{--                    <td>{{ $item->sale_price }}</td>--}}
{{--                    <td>--- </td>--}}
{{--                    <td> ---- </td>--}}
{{--                    <td> ---- </td>--}}
{{--                </tr>--}}
{{--                @empty--}}
{{--                <tr>--}}
{{--                    <td colspan="10">لا توجد بيانات</td>--}}
{{--                </tr>--}}
{{--                @endforelse--}}
{{--            </tbody>--}}
{{--        </table>--}}
{{--        <br />--}}
{{--        <br />--}}
{{--        <table class="table table-bordered foot-table table-hover" style="width: 50%; margin: 0 auto;">--}}
{{--            <tr>--}}
{{--                <td> المجاميع القيمة: </td>--}}
{{--                <td style="width: 75%;"> {{ $item->sale_price ?? " 0000" }} </td>--}}
{{--                <td style="width: 5%;"> ل.س </td>--}}
{{--            </tr>--}}
{{--        </table>--}}

{{--        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>--}}
{{--    </div> <!-- end class containerr-->--}}
{{--</body>--}}

{{--</html>--}}
    <!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>بطاقة المذكرة {{ $bill->bill_number ?? 'غير محدد' }}</title>
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
            text-align: right;
        }

        .faculty-name {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            text-align: right;
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
        td,
        tr {
            font-weight: bold;
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
            font-size: 20px;
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

        .note-subject1 {
            font-weight: bold;
            text-align: center;
            font-size: 22px;
        }

        .footer {
            text-align: right;
            font-weight: bold;
            font-size: 25px;
            margin-right: 3rem;
            width: 100%;

        }
        .names{
            margin-right: 20rem;
        }
    </style>
</head>

<body>

@php

<<<<<<< HEAD
    <div class="containerr">
        <div class="row">
            <div class="col-5">
                <div class="title">الجمهورية العربية السورية</div>
                <div class="subtitle" style="margin: 3px 55px 0 0 ;">وزارة المالية</div>
            </div> <!-- end class col-5 -->

            <div class="col-3">
                <div class="subtitle"> مذكرة {{ $bill ->type }} </div>
                <!-- <div class="subtitle"> مذكرة استلام </div> -->
                <div class="faculty-name">{{ $bill->destinationWarehouse ?? ' كلية الطب البشري '}}</div>
            </div> <!-- end class col-3 -->

            <div class="col-2">
                <div class="item-line" style="margin-top: 30px;"><strong>{{ $bill?->bill_number ?? '0' }}</strong></div>
            </div> <!-- end class col-2 -->


            <div class="col-2">
                <div class="model-number"><strong>نموذج مستودع رقم (15)</strong></div>
                <div style="margin-top: 8px;">
                    <div class="item-line"><strong>رقم :</strong></div>
                </div>
            </div> <!-- end class col-2 -->


        </div> <!-- end class row -->
        <br />
        <div class="note">
            <p class="note-subject1">
                إن المواد المذكورة أدناه وردت من <sapn class="note-subject1" style="margin-right: 10rem;">التاريخ </sapn> <span style="margin-right: 2rem;">/</span> <span style="margin-right: 2rem;">/</span> <span style="margin-right: 1rem;">202 </span> <br>
                بموجب الفاتورة رقم <sapn class="note-subject1" style="margin-right: 8rem;">تاريخ</sapn>
                </sapn> <span style="margin-right: 2rem;">/</span> <span style="margin-right: 2rem;">/</span> <span style="margin-right: 1rem;">202 </span>
            </p> <!-- end class note-subject1 -->
        </div> <!-- end class note -->

        <table class="table table-bordered table-hover">
            <thead>
                <tr class="table-active">
                    <th colspan="5"> المادة </th>
                    <th scope="col" rowspan="2">الكمية المستلمة</th>
                    <th scope="col" rowspan="2">السعر</th>
                    <th scope="col" rowspan="2">القيمة</th>
                    <th scope="col" rowspan="2">رقم البطاقة</th>
                    <th scope="col" rowspan="2">ملاحظات</th>
                </tr>
                <tr class="table-active">
                    <th scope="col">الرقم المتسلسل</th>
                    <th scope="col">رمزها</th>
                    <th scope="col">اسمها</th>
                    <th scope="col">أوصافها</th>
                    <th scope="col">وحدتها</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bill->billRecords as $index => $record)
                <tr>
                    <td></td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td> ---- </td>
                    <td>{{ $item->unit }}</td>
                    <td> ---- </td>
                    <td>{{ $item->sale_price }}</td>
                    <td>--- </td>
                    <td> ---- </td>
                    <td> ---- </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">لا توجد بيانات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <br />
        <br />
        <table class="table table-bordered foot-table table-hover" style="width: 50%; margin: 0 auto;">
=======
    $totalValue = 0;
@endphp

<div class="containerr">
    <div class="row">
        <div class="col-5">
            <div class="title">الجمهورية العربية السورية</div>
            <div class="subtitle" style="margin: 3px 55px 0 0 ;">وزارة المالية</div>
        </div>
        <div class="col-4">
            <div class="subtitle"> مذكرة {{ match($bill->type) { 'purchase' => 'شراء', 'transfer' => 'تحويل', 'adjustment' => 'تعديل', 'return' => 'مرتجع', default => $bill->type } }} </div>
            <div class="faculty-name">{{ $bill->destinationWarehouse->name ?? $bill->sourceWarehouse->name ?? 'كلية الطب البشري' }}</div>
        </div>
        <div class="col-3">
            <div class="model-number"><strong>نموذج مستودع رقم (15)</strong></div>
            <div class="item-line"><strong>رقم المجلد :</strong> {{ $bill->reference_number ?? 'ورق أبيض A4 غراماج' }}</div>
            <div class="item-line"><strong>تاريخ المذكرة:</strong> {{ $bill && $bill->date ? \Carbon\Carbon::parse($bill->date)->format('d/m/Y') : '1111' }}</div>
            <div style="margin-top: 8px;">
                <div class="item-line"><strong>رقم :</strong> {{ $bill->bill_number ?? '0' }}</div>
            </div>
        </div>
    </div>
    <br />
    <div class="note">
        <p class="note-subject1">
            إن المواد المذكورة أدناه وردت من مديرية الكتب بموجب الفاتورة رقم
            {{ $bill->reference_number ?? $bill->bill_number }}
            تاريخ {{ $bill && $bill->reference_date ? \Carbon\Carbon::parse($bill->reference_date)->format('d/m/Y') : ($bill->date ? \Carbon\Carbon::parse($bill->date)->format('d/m/Y') : '---') }}
        </p>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
        <tr class="table-active">
            <th colspan="5"> المادة </th>
            <th scope="col" rowspan="2">الكمية المستلمة</th>
            <th scope="col" rowspan="2">السعر</th>
            <th scope="col" rowspan="2">القيمة</th>
            <th scope="col" rowspan="2">رقم البطاقة</th>
            <th scope="col" rowspan="2">ملاحظات</th>
        </tr>
        <tr class="table-active">
            <th scope="col">الرقم المتسلسل</th>
            <th scope="col">رمزها</th>
            <th scope="col">اسمها</th>
            <th scope="col">أوصافها</th>
            <th scope="col">وحدتها</th>
        </tr>
        </thead>
        <tbody>
        @forelse($bill->billRecords as $index => $record)
            @php
                $item = $record->item;
                $lineTotal = $record->quantity * $record->unit_price;
                $totalValue += $lineTotal;
            @endphp
>>>>>>> 162e95eed48bb2287ed0ab8ace848d01de8700b4
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->code ?? '---' }}</td>
                <td>{{ $item->name ?? '---' }}</td>
                <td>{{ $record->notes ?? '---' }}</td>
                <td>{{ $item->unit ?? '---' }}</td>
                <td>{{ number_format($record->quantity, 2) }}</td>
                <td>{{ number_format($record->unit_price, 2) }}</td>
                <td>{{ number_format($lineTotal, 2) }}</td>
                <td>{{ $record->batch_number ?? '---' }}</td>
                <td>{{ $record->notes ?? '---' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10">لا توجد بيانات</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <br />
    <br />
    <table class="table table-bordered foot-table table-hover" style="width: 50%; margin: 0 auto;">
        <tr>
            <td> المجاميع القيمة: </td>
            <td style="width: 75%;"> {{ number_format($totalValue, 2) }} </td>
            <td style="width: 5%;"> ل.س </td>
        </tr>
    </table>

<<<<<<< HEAD
        <div class="footer">
            <p>فقــط كميــة قدرهــا .................... وقيمتهــا مبلــغ ....................<p>
            
            <span>تم استـلام المـواد المبينـة أعـلاه وفقـاً للمواصفـات المـحددة فـي .................... رقــم .................... تاريــخ</span> 
            <span style="margin-right: 4rem;">/</span> <span style="margin-right: 2rem;">/</span> <span style="margin-right: 1rem;">202 </span> <br>
            
            <p style="margin-top: 15px;">ويـجري استلامهــا فــي .................... وتــم تسجيلهــا فـي بطاقـات المســتودع المشــار إليهــا</p>
            <br>
        <div class="row names">
            <div class="col-6">
                <p>المسلــم: </p>
                <p>الاسم : </p>
                <p> التوقيع: </p>
            </div> <!-- end class col-6 -->

            <div class="col-6">
                <p>أمــين المســتودع المستلــم</p>
                <p>الاسم : </p>
                <p>التوقيع : </p>
            </div> <!-- end class col-6 -->

        </div> <!--end class footer -->

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </div> <!-- end class containerr-->
</body>

</html>



        </div> <!-- end class row -->
=======
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>

</html>
>>>>>>> 162e95eed48bb2287ed0ab8ace848d01de8700b4
