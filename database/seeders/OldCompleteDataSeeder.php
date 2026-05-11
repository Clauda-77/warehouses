<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Bill;
use App\Models\BillRecord;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class OldCompleteDataSeeder extends Seeder
{

    public function run(): void
    {


        $admin = User::where('username', 'admin')->first();
        $warehouse = Warehouse::first();



        if (!$warehouse) {
            $warehouse = Warehouse::create([
                'code' => 'MAIN',
                'name' => 'المخزن الرئيسي',
                'type' => 'central',
                'is_active' => true,
            ]);
            $this->command->info('OK: تم إنشاء المخزن الرئيسي.');
        } else {
            $this->command->info('OK: تم العثور على المخزن: ' . $warehouse->name);
        }

        DB::beginTransaction();

        try {

             $rootCat = Category::firstOrCreate(['name' => 'الجامعة - رئيسي'], ['code' => 'ROOT']);

            $categories = [
                ['code' => 'MED', 'name' => 'كلية الطب البشري'],
                ['code' => 'ENG', 'name' => 'كلية الهندسة المعلوماتية'],
                ['code' => 'ECO', 'name' => 'كلية الاقتصاد'],
                ['code' => 'INST', 'name' => 'مديرية المعاهد'],
                ['code' => 'WARE', 'name' => 'المستودعات'],
                ['code' => 'OFF', 'name' => 'المكاتب الإدارية'],
            ];

            $catMap = [];
            foreach ($categories as $cat) {
                $model = Category::firstOrCreate(
                    ['code' => $cat['code']],
                    [
                        'name' => $cat['name'],
                        'parent_id' => $rootCat->id,
                        'is_active' => true,
                    ]
                );
                $catMap[$cat['code']] = $model->id;

                $model->touch();
            }



            $itemsData = [
                ['code' => '1111', 'name' => 'ورق أبيض A4 80g', 'unit' => 'ماعون', 'cat_code' => 'MED'],
                ['code' => '1111-70', 'name' => 'ورق أبيض A4 70g', 'unit' => 'ماعون', 'cat_code' => 'MED'],
                ['code' => '1113', 'name' => 'دفتر امتحاني 8 صفحات', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1113-REC', 'name' => 'دفتر ايصال رسوم', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1113-MON', 'name' => 'جلد حركة مواد', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1114', 'name' => 'اوراق امنية A5', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1114-SEC', 'name' => 'ورق أمني خاص', 'unit' => 'عدد', 'cat_code' => 'ECO'],
                ['code' => '1112-FOL', 'name' => 'كلاسور 8 سم', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1112-ENV-L', 'name' => 'ظرف أسمر كبير', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1112-ENV-M', 'name' => 'ظرف أسمر وسط', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1112-ENV-S', 'name' => 'ظرف أسمر صغير', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1121-PEN', 'name' => 'قلم حبر ناشف', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1121-PENCIL', 'name' => 'قلم رصاص', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1121-HIGHLIGHT', 'name' => 'قلم مؤشر ملون', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1121-PLAN', 'name' => 'قلم تخطيط', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1121-ERASER', 'name' => 'قلم ماحي', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1131', 'name' => 'مسطرة بلاستيك', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1133-STAPLE', 'name' => 'شريط غرز وسط', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1133-CLIP', 'name' => 'شكالات وسط', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1133-CLIP-L', 'name' => 'شكالات كبيرة', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1134', 'name' => 'ممحاة', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1134-SHARP', 'name' => 'مبراة', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1123-TAPE', 'name' => 'لاصق شفاف 2 سم', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1123-TAPE-5', 'name' => 'لاصق شفاف 5 سم', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1123-GLUE', 'name' => 'صمغ سائل', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1122-HP', 'name' => 'حبر طابعة HP 1020', 'unit' => 'عبوة', 'cat_code' => 'MED'],
                ['code' => '1122-BRO', 'name' => 'حبر طابعة برازر', 'unit' => 'عبوة', 'cat_code' => 'MED'],
                ['code' => '1122-CAN', 'name' => 'حبر طابعة كانون', 'unit' => 'عبوة', 'cat_code' => 'MED'],
                ['code' => '1122-PAN', 'name' => 'حبر طابعة Pantum', 'unit' => 'عبوة', 'cat_code' => 'ENG'],
                ['code' => '1122-INK', 'name' => 'حبر ريكو', 'unit' => 'عبوة', 'cat_code' => 'ENG'],
                ['code' => '1122-BLOCK', 'name' => 'حبر اسطمبة', 'unit' => 'عبوة', 'cat_code' => 'MED'],
                ['code' => '1413-DET', 'name' => 'سائل جلي', 'unit' => 'عبوة', 'cat_code' => 'MED'],
                ['code' => '1413-BLEACH', 'name' => 'سائل جافيل', 'unit' => 'عبوة', 'cat_code' => 'ENG'],
                ['code' => '1413-PH', 'name' => 'سائل PH بخاخ', 'unit' => 'عبوة', 'cat_code' => 'ENG'],
                ['code' => '1413-CLOR', 'name' => 'كلور تنظيف', 'unit' => 'عبوة', 'cat_code' => 'MED'],
                ['code' => '1413-SOAP', 'name' => 'صابون سائل', 'unit' => 'عبوة', 'cat_code' => 'MED'],
                ['code' => '1413-BAG', 'name' => 'اكياس قمامة', 'unit' => 'كغ', 'cat_code' => 'MED'],
                ['code' => '1413-GLASS', 'name' => 'ملمع زجاج', 'unit' => 'عبوة', 'cat_code' => 'MED'],
                ['code' => '1412-BRUSH', 'name' => 'فرشاة تواليت', 'unit' => 'عدد', 'cat_code' => 'WARE'],
                ['code' => '1412-SWA', 'name' => 'مكنسة ناعمة', 'unit' => 'عدد', 'cat_code' => 'WARE'],
                ['code' => '1412-BROOM', 'name' => 'قشاطة', 'unit' => 'عدد', 'cat_code' => 'WARE'],
                ['code' => '1412-MOP1', 'name' => 'ممسحة ارضية', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1412-BIN', 'name' => 'سلة مهملات', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1412-COVER', 'name' => 'غطاء صرف', 'unit' => 'عدد', 'cat_code' => 'MED'],
                ['code' => '1623-SWEEPER', 'name' => 'سراقة كهربائية', 'unit' => 'عدد', 'cat_code' => 'WARE'],
                ['code' => '1322-CABLE', 'name' => 'كبل طابعة', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1322-USB', 'name' => 'وصلة USB', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1322-MOUSE', 'name' => 'ماوس ليزرية', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1322-DVD', 'name' => 'قرص DVD', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1313-COP', 'name' => 'الة تصوير كانون', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1314-PRT', 'name' => 'طابعة برازر', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1314-PRT-PAN', 'name' => 'طابعة ليزرية Pantum', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '1352-CALC', 'name' => 'الة حاسبة', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1352-FAN', 'name' => 'مروحة عامودية', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1331-PHONE', 'name' => 'جهاز هاتف', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1212-TABLE', 'name' => 'طاولة آلة تصوير', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1212-CHAIR', 'name' => 'كرسي هيدروليك', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1212-SOFA', 'name' => 'كنبة مفردة', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1213-CAB', 'name' => 'خزانة خشبية', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1234-DESK', 'name' => 'طاولة مكتب', 'unit' => 'عدد', 'cat_code' => 'INST'],
                ['code' => '1226-MAT', 'name' => 'حرام صوف', 'unit' => 'عدد', 'cat_code' => 'WARE'],
                ['code' => '3029-COV', 'name' => 'جلد مذكرة تسليم', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '3030-COV', 'name' => 'جلد مذكرة ادخال', 'unit' => 'عدد', 'cat_code' => 'ENG'],
                ['code' => '553-FERT', 'name' => 'سماد يوريا', 'unit' => 'كغ', 'cat_code' => 'WARE'],
            ];

            $itemIds = [];

            foreach ($itemsData as $item) {
                $createdItem = Item::firstOrCreate(
                    ['code' => $item['code']],
                    [
                        'name' => $item['name'],
                        'unit' => $item['unit'],
                        'category_id' => $catMap[$item['cat_code']],
                        'purchase_price' => 0,
                        'sale_price' => 0,
                        'opening_balance' => 0,
                        'current_quantity' => 0,
                        'created_by' => $admin->id,
                    ]
                );
                $itemIds[] = $createdItem->id;

                $createdItem->touch();
            }

            $bill = Bill::create([
                'bill_number' => 'OLD-DATA-IMPORT-' . date('Ymd'),
                'date' => '2024-10-14',
                'type' => 'purchase',
                'status' => 'approved',
                'supplier_id' => null,
                'total' => 0,
                'created_by' => $admin->id,
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);

             foreach ($itemIds as $itemId) {
                BillRecord::create([
                    'bill_id' => $bill->id,
                    'item_id' => $itemId,
                    'quantity' => 50,
                    'unit_price' => 1000,
                    'warehouse_id' => $warehouse->id,
                ]);
            }

            $total = BillRecord::where('bill_id', $bill->id)->sum('total_price');
            $bill->update(['total' => $total]);


            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

        }
    }

}
