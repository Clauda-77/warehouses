<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillRecord extends Model
{


    protected $fillable = [
        'bill_id', 'item_id', 'item_code', 'batch_number', 'unit',
        'quantity', 'unit_price', 'total_price', 'notes',
        'warehouse_id', 'cost_price', 'stock_before', 'stock_after'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
    ];




   public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }


    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->received_quantity;
    }

    public function isFullyReceived()
    {
        return $this->quantity == $this->received_quantity;
    }

      public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

}
