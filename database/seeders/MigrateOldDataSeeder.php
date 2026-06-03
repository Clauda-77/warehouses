<?php
//
//namespace Database\Seeders;
//
//use Illuminate\Database\Seeder;
//use App\Models\User;
//use App\Models\Category;
//use App\Models\Item;
//use App\Models\Warehouse;
//use App\Models\Bill;
//use App\Models\BillRecord;
//use Illuminate\Support\Facades\DB;
//
//class MigrateOldDataSeeder extends Seeder
//{
//    public function run(): void
//    {
//        $this->command->info('🚀 بدء ترحيل البيانات من الملف القديم...');
//
//        // 1. التأكد من وجود المستخدم والمستودع الرئيسي
//        $admin = User::firstOrCreate(['username' => 'admin'], ['name' => 'Admin', 'email' => 'admin@example.com']);
//        $warehouse = Warehouse::firstOrCreate(['code' => 'MAIN'], ['name' => 'المستودع الرئيسي', 'type' => 'central', 'is_active' => true]);
//
//        // 2. إنشاء فئة افتراضية للأصناف المستوردة
//        $category = Category::firstOrCreate(['code' => 'IMPORTED'], ['name' => 'بيانات مستوردة', 'is_active' => true]);
//
//        // 3. قراءة ملف data.txt
//        $filePath = base_path('database/seeders/data.txt');
//        if (!file_exists($filePath)) {
//            $this->command->error("❌ الملف غير موجود: " . $filePath);
//            return;
//        }
//
//        $handle = fopen($filePath, 'r');
//        if (!$handle) {
//            $this->command->error("❌ لا يمكن فتح الملف");
//            return;
//        }
//
//        $items = [];      // لتخزين الأصناف الفريدة
//        $bills = [];      // لتخزين الفواتير الفريدة
//        $records = [];    // لتخزين تفاصيل الفواتير
//        $currentBill = null;
//        $lineNumber = 0;
//
//        while (($line = fgets($handle)) !== false) {
//            $lineNumber++;
//            $line = trim($line);
//
//            // البحث عن أسطر INSERT الخاصة بجدول DETBILCST
//            if (strpos($line, 'INSERT INTO "DETBILCST"') !== false) {
//                // استخراج الأرقام بين VALUES
//                if (preg_match('/VALUES \((.*?)\)/s', $line, $matches)) {
//                    $valuesString = $matches[1];
//
//                    // استخراج الأرقام :1, :2, :3 ... وترتيبها
//                    preg_match_all('/:(\d+)/', $valuesString, $valueMatches);
//                    $values = $valueMatches[1];
//
//                    if (count($values) >= 20) {
//                        // تعيين المتغيرات حسب ترتيب الأعمدة في الجدول الأصلي
//                        $bilno   = $values[1] ?? null;      // رقم الفاتورة
//                        $prtno   = $values[3] ?? null;      // رقم المنتج
//                        $prtname = $values[4] ?? 'غير معروف'; // اسم المنتج
//                        $price   = floatval($values[6] ?? 0); // السعر
//                        $qty     = floatval($values[7] ?? 0); // الكمية
//                        $unit    = $values[8] ?? 'قطعة';      // الوحدة
//                        $jiha    = $values[15] ?? 'المستودع'; // الجهة
//
//                        if ($bilno && $prtno) {
//                            // تجميع الأصناف
//                            if (!isset($items[$prtno])) {
//                                $items[$prtno] = [
//                                    'code' => $prtno,
//                                    'name' => $prtname,
//                                    'unit' => $unit,
//                                ];
//                            }
//
//                            // تجميع الفواتير
//                            if (!isset($bills[$bilno])) {
//                                $bills[$bilno] = [
//                                    'number' => $bilno,
//                                    'warehouse' => $jiha,
//                                ];
//                            }
//
//                            // تجميع تفاصيل الفاتورة
//                            $records[] = [
//                                'bilno' => $bilno,
//                                'prtno' => $prtno,
//                                'qty' => $qty,
//                                'price' => $price,
//                            ];
//                        }
//                    }
//                }
//            }
//        }
//        fclose($handle);
//
//        $this->command->info("📊 تم العثور على:");
//        $this->command->info("   - عدد الأصناف: " . count($items));
//        $this->command->info("   - عدد الفواتير: " . count($bills));
//        $this->command->info("   - عدد تفاصيل الفواتير: " . count($records));
//
//        // 4. إدراج البيانات في قاعدة البيانات
//        DB::beginTransaction();
//        try {
//            // إدراج الأصناف
//            $itemIds = [];
//            foreach ($items as $code => $item) {
//                $created = Item::updateOrCreate(
//                    ['code' => $code],
//                    [
//                        'name' => $item['name'],
//                        'unit' => $item['unit'],
//                        'category_id' => $category->id,
//                        'purchase_price' => 0,
//                        'current_quantity' => 0,
//                        'created_by' => $admin->id,
//                    ]
//                );
//                $itemIds[$code] = $created->id;
//            }
//
//            // إدراج الفواتير
//            $billIds = [];
//            foreach ($bills as $number => $bill) {
//                $created = Bill::create([
//                    'bill_number' => $number,
//                    'date' => now(),
//                    'type' => 'purchase',
//                    'status' => 'approved',
//                    'supplier_id' => null,
//                    'total' => 0,
//                    'created_by' => $admin->id,
//                    'approved_by' => $admin->id,
//                    'approved_at' => now(),
//                ]);
//                $billIds[$number] = $created->id;
//            }
//
//            // إدراج تفاصيل الفواتير
//            foreach ($records as $record) {
//                if (isset($itemIds[$record['prtno']]) && isset($billIds[$record['bilno']])) {
//                    BillRecord::create([
//                        'bill_id' => $billIds[$record['bilno']],
//                        'item_id' => $itemIds[$record['prtno']],
//                        'quantity' => $record['qty'],
//                        'unit_price' => $record['price'],
//                        'total_price' => $record['qty'] * $record['price'],
//                        'warehouse_id' => $warehouse->id,
//                    ]);
//                }
//            }
//
//            DB::commit();
//            $this->command->info("✅ تم ترحيل جميع البيانات بنجاح!");
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//            $this->command->error("❌ خطأ أثناء الترحيل: " . $e->getMessage());
//        }
//    }
//}


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\Bill;
use App\Models\BillRecord;
use Illuminate\Support\Facades\DB;

class MigrateOldDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 بدء ترحيل البيانات من ملف data.txt...');


        $admin = User::firstOrCreate(['username' => 'admin'], ['name' => 'Admin', 'email' => 'admin@example.com']);
        $warehouse = Warehouse::firstOrCreate(['code' => 'MAIN'], ['name' => 'المستودع الرئيسي', 'type' => 'central', 'is_active' => true]);
        $category = Category::firstOrCreate(['code' => 'IMPORTED'], ['name' => 'بيانات مستوردة', 'is_active' => true]);

        $filePath = base_path('database/seeders/data.txt');
        if (!file_exists($filePath)) {
            $this->command->error("❌ الملف غير موجود: " . $filePath);
            return;
        }

        $fileContent = file_get_contents($filePath);


        $pattern = '/INSERT\s+INTO\s+"DETBILCST".*?VALUES\s*\((.*?)\);/s';

        if (!preg_match_all($pattern, $fileContent, $matches)) {
            $this->command->warn("⚠️ لم يتم العثور على أي جمل INSERT صالحة للقراءة في الملف.");
            $this->command->warn("إذا كان الملف يحتوي على (:1, :2...) فهذا يعني أنه Binary Dump ولا يمكن قراءته بهذه الطريقة.");
            return;
        }

        $items = [];
        $bills = [];
        $records = [];

         foreach ($matches[1] as $valuesString) {

             if (strpos($valuesString, ':') !== false && strpos($valuesString, ':1') !== false) {

                $this->command->warn("⚠️ تم العثور على بيانات بصيغة Binary (:1, :2...) لا يمكن استخراجها برمجياً.");

                continue;
            }

             $values = $this->parseSqlValues($valuesString);

             if (count($values) < 16) continue;

            $bilno = $values[1] ?? null;
            $prtno = $values[3] ?? null;
            $prtname = $values[4] ?? 'غير معروف';
            $price = floatval($values[6] ?? 0);
            $qty = floatval($values[7] ?? 0); // الكمية
            $unit = $values[8] ?? 'قطعة';      // الوحدة
            $jiha = $values[15] ?? 'المستودع'; // الجهة (Index 15)

            if ($bilno && $prtno) {

                if (!isset($items[$prtno])) {
                    $items[$prtno] = [
                        'code' => $prtno,
                        'name' => $prtname,
                        'unit' => $unit,
                    ];
                }

                 if (!isset($bills[$bilno])) {
                    $bills[$bilno] = [
                        'number' => $bilno,
                        'warehouse' => $jiha,
                    ];
                }

                 $records[] = [
                    'bilno' => $bilno,
                    'prtno' => $prtno,
                    'qty' => $qty,
                    'price' => $price,
                ];
            }
        }

        $this->command->info("📊 تم العثور على:");
        $this->command->info("   - عدد الأصناف: " . count($items));
        $this->command->info("   - عدد الفواتير: " . count($bills));
        $this->command->info("   - عدد السجلات: " . count($records));

        if (count($records) == 0) {
            $this->command->error("❌ لم يتم استيراد أي بيانات. تأكد من أن ملف data.txt يحتوي على قيم نصية وليس رموزاً ثنائية.");
            return;
        }


        DB::beginTransaction();
        try {
            $itemIds = [];
            foreach ($items as $code => $item) {
                $created = Item::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $item['name'],
                        'unit' => $item['unit'],
                        'category_id' => $category->id,
                        'purchase_price' => 0,
                        'current_quantity' => 0,
                        'created_by' => $admin->id,
                    ]
                );
                $itemIds[$code] = $created->id;
            }

            $billIds = [];
            $billTotals = [];

            foreach ($bills as $number => $bill) {
                $created = Bill::create([
                    'bill_number' => $number,
                    'date' => now(),
                    'type' => 'purchase',
                    'status' => 'approved',
                    'supplier_id' => null,
                    'total' => 0,
                    'created_by' => $admin->id,
                    'approved_by' => $admin->id,
                    'approved_at' => now(),
                ]);
                $billIds[$number] = $created->id;
                $billTotals[$number] = 0;
            }

            foreach ($records as $record) {
                if (isset($itemIds[$record['prtno']]) && isset($billIds[$record['bilno']])) {
                    $totalRow = $record['qty'] * $record['price'];
                    $billTotals[$record['bilno']] += $totalRow;

                    BillRecord::create([
                        'bill_id' => $billIds[$record['bilno']],
                        'item_id' => $itemIds[$record['prtno']],
                        'quantity' => $record['qty'],
                        'unit_price' => $record['price'],
                        'warehouse_id' => $warehouse->id,
                        // ملاحظة: total_price تم حذفه لأنه محسوب تلقائياً في قاعدة البيانات
                    ]);
                }
            }

            // تحديث مجاميع الفواتير
            foreach ($billTotals as $bilno => $total) {
                if (isset($billIds[$bilno]) && $total > 0) {
                    Bill::where('id', $billIds[$bilno])->update([
                        'subtotal' => $total,
                        'total' => $total
                    ]);
                }
            }

            DB::commit();
            $this->command->info("✅ تم ترحيل البيانات بنجاح!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ خطأ أثناء الترحيل: " . $e->getMessage());
            $this->command->error("File: " . $e->getFile() . " Line: " . $e->getLine());
        }
    }

    private function parseSqlValues($str)
    {
        $values = [];
        $current = '';
        $inQuotes = false;
        $len = strlen($str);

        for ($i = 0; $i < $len; $i++) {
            $char = $str[$i];

            if ($char === "'") {
                $inQuotes = !$inQuotes;
                $current .= $char;
            } elseif ($char === ',' && !$inQuotes) {

                $values[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }

        $values[] = trim($current);

        return $values;
    }
}
