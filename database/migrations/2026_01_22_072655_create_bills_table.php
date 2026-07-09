<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BillStatus;
use App\Enums\BillType; 



return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {  //  MSTBILSRC/MSTBILCST
            $table->id();
            $table->string('bill_number')->unique();   //   BILNO
            $table->date('date');                      //   BILDATE


            // $table->string('type');                    // purchase/sale/transfer/return
            // $table->string('status')->default('draft'); // draft/pending/completed/cancelled


            $table->string('type')->default(BillType::PURCHASE->value);
            $table->string('status')->default(BillStatus::DRAFT->value);
             $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
             $table->string('party_name')->nullable();  //   ACCNAME
            
             $table->foreignId('source_warehouse_id')->nullable()->constrained('warehouses'); 
            $table->foreignId('destination_warehouse_id')->nullable()->constrained('warehouses');  
            
             $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0); //   BILVALUE
            
      
            $table->string('reference_number')->nullable(); //   NUMFATOURA
            $table->date('reference_date')->nullable();     //   DATEFATOURA
            $table->text('notes')->nullable();              //   REMARQUE
            
      
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
       
            $table->index('bill_number');
            $table->index('date');
            $table->index('type');
            $table->index('status');
            $table->index(['source_warehouse_id', 'destination_warehouse_id']);
                    });
    }

     
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
