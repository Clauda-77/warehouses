<?php


namespace App\Enums;

enum UserRole: string
{


    case ADMIN = 'admin'; // فقط إشراف لا يعدل بشي - يعني قادر يعرف إحصائيات 
    case MONITOR = 'monitor'; //  صلاحيات كاملة  كاملة - حذف إضافة تعديل-  مراقب - مدقق - 
    case WAREHOUSE_KEEPER = 'warehouse_keeper'; // أمين المستودع   متل المراقب بس لا بيعدل مادة و لا بيحذفها  وما بيضيف - بيختار المادة 
        // يعني متل نهاد عنا 

    case SUPERVISOR = 'supervisor';


    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'مدير النظام',
            self::MONITOR => 'مراقب',
            self::WAREHOUSE_KEEPER => 'أمين مستودع',
            self::SUPERVISOR => 'مدير إشراف',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::MONITOR => 'success',
            self::WAREHOUSE_KEEPER => 'info',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => [

                'view_dashboard',
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'assign_roles',
                'manage_roles',
                'manage_permissions',
                'view_items',
                'create_items',
                'edit_items',
                'delete_items',
                'import_items',
                'export_items',
                'view_warehouses',
                'create_warehouses',
                'edit_warehouses',
                'delete_warehouses',
                'view_bills',
                'create_bills',
                'edit_bills',
                'delete_bills',
                'approve_bills',
                'cancel_bills',
                'print_bills',
                'view_transfers',
                'create_transfers',
                'edit_transfers',
                'approve_transfers',
                'receive_transfers',
                'view_reports',
                'generate_reports',
                'export_reports',
                'view_customers',
                'create_customers',
                'edit_customers',
                'delete_customers',
                'view_suppliers',
                'create_suppliers',
                'edit_suppliers',
                'delete_suppliers',
            ],

            self::WAREHOUSE_KEEPER => [

                'view_dashboard',
                'view_items',
                'create_items',
                'edit_items',
                'delete_items',
                'import_items',
                'export_items',
                'view_warehouses',
                'create_warehouses',
                'edit_warehouses',
                'delete_warehouses',
                'view_bills',
                'create_bills',
                'edit_bills',
                'delete_bills',
                'approve_bills',
                'cancel_bills',
                'print_bills',
                'view_transfers',
                'create_transfers',
                'edit_transfers',
                'approve_transfers',
                'receive_transfers',
                'view_reports',
                'generate_reports',
                'export_reports',
                'view_customers',
                'create_customers',
                'edit_customers',
                'delete_customers',
                'view_suppliers',
                'create_suppliers',
                'edit_suppliers',
                'delete_suppliers',
            ],

            self::MONITOR => [

                'view_dashboard',
                'view_items',
                'view_warehouses',
                'create_warehouses',
                'edit_warehouses',
                'delete_warehouses',
                'view_bills',
                'create_bills',
                'edit_bills',
                'delete_bills',
                'approve_bills',
                'cancel_bills',
                'print_bills',
                'view_transfers',
                'create_transfers',
                'edit_transfers',
                'approve_transfers',
                'receive_transfers',
                'view_reports',
                'generate_reports',
                'export_reports',
                'view_customers',
                'create_customers',
                'edit_customers',
                'delete_customers',
                'view_suppliers',
                'create_suppliers',
                'edit_suppliers',
                'delete_suppliers',
            ],

            self::SUPERVISOR => [
                'view_dashboard',
                'view_users',
                'view_items',
                'view_warehouses',
                'view_bills',
                'print_bills',
                'view_transfers',
                'view_reports',
                'generate_reports',
                'export_reports', // يمكنه عرض وتصدير التقارير
                'view_customers',
                'view_suppliers',
            ],
        };
    }
}
