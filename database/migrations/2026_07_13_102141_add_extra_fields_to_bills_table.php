<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->string('financial_number')->nullable()->after('bill_number'); 
            $table->string('invoice_number')->nullable()->after('financial_number'); 
            $table->date('invoice_date')->nullable()->after('invoice_number');  
            $table->string('purchase_order_number')->nullable()->after('invoice_date');  
            $table->date('purchase_order_date')->nullable()->after('purchase_order_number');  
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn([
                'financial_number',
                'invoice_number',
                'invoice_date',
                'purchase_order_number',
                'purchase_order_date',
            ]);
        });
    }
};