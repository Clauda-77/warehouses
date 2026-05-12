<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

        use HasFactory, SoftDeletes;

    protected $fillable = [
        'bill_number', 'date', 'type', 'status', 'supplier_id', 'customer_id',
        'party_name', 'source_warehouse_id', 'destination_warehouse_id',
        'subtotal', 'discount', 'tax', 'total', 'reference_number',
        'reference_date', 'notes', 'created_by', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'date' => 'date',
        'reference_date' => 'date',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sourceWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function billRecords()
    {
        return $this->hasMany(BillRecord::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

       public function category()
    {
        return $this->belongsTo(Category::class);
    }



    public function lastUpdater()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }


    public function getAvailableQuantityAttribute()
    {
        return $this->current_quantity - $this->reserved_quantity;
    }

    public function updateTotals(): void
{
    $subtotal = $this->billRecords()->sum('total_price');
    $total = $subtotal - $this->discount + $this->tax;

    $this->update([
        'subtotal' => $subtotal,
        'total' => $total,
    ]);
}
}
