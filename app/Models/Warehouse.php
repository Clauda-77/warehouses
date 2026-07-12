<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Factories\HasFactory;
 
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\WarehouseType;



class Warehouse extends Model
{
      
       use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'location',
        'contact_person',
        'phone',
        'is_active',
        'total_items',
        'total_value',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_value' => 'decimal:2',
    ];

 
    public function parent()
    {
        return $this->belongsTo(Warehouse::class, 'parent_id');
    }

     public function branches()
    {
        return $this->hasMany(Warehouse::class, 'parent_id');
    }

     public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

     public function scopeCentral($query)
    {
        return $query->where('type', 'central');
    }

     public function scopeBranch($query)
    {
        return $query->where('type', 'branch');
    }

     public function getFullNameAttribute()
    {
        return "{$this->code} - {$this->name}";
    }
 

    public function children()
    {
        return $this->hasMany(Warehouse::class, 'parent_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'warehouse_stocks')
                    ->withPivot(['current_quantity', 'reserved_quantity', 'minimum_quantity', 'maximum_quantity'])
                    ->withTimestamps();
    }
  public function sourceBills()
    {
        return $this->hasMany(Bill::class, 'source_warehouse_id');
    }
    
    public function destinationBills()
    {
        return $this->hasMany(Bill::class, 'destination_warehouse_id');
    }
    
    public function billRecords()
    {
        return $this->hasMany(BillRecord::class);
    }
     public function generateCode(): void
    {
        if (empty($this->code)) {
            $prefix = match($this->type) {
                WarehouseType::CENTRAL => 'WH-C-',
                WarehouseType::BRANCH => 'WH-B-',
                WarehouseType::STORE => 'WH-S-',
                WarehouseType::TEMPORARY => 'WH-T-',
                
            };
            
            $this->code = $prefix . str_pad($this->id, 4, '0', STR_PAD_LEFT);
        }
    }

    public function getAvailableCapacity(): int
    {
        return $this->capacity - $this->total_items;
    }

    public function getOccupancyRate(): float
    {
        if ($this->capacity <= 0) return 0;
        return ($this->total_items / $this->capacity) * 100;
    }

    public function getAllChildrenIds(): array
    {
        $ids = [$this->id];
        
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }
        
        return $ids;
    }

    public function canHaveChildren(): bool
    {
        return in_array($this->type, [
            WarehouseType::CENTRAL,
            WarehouseType::BRANCH,
        ]);
    }

 
     
 
 

    public function scopeWithCapacity($query, $minCapacity = 0)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($warehouse) {
             if (empty($warehouse->code)) {
                $lastWarehouse = self::orderBy('id', 'desc')->first();
                $nextId = $lastWarehouse ? $lastWarehouse->id + 1 : 1;
                
                $prefix = match($warehouse->type) {
                    WarehouseType::CENTRAL => 'WH-C-',
                    WarehouseType::BRANCH => 'WH-B-',
                    WarehouseType::STORE => 'WH-S-',
                    WarehouseType::TEMPORARY => 'WH-T-',
                    default => 'WH-G-',
                };
                
                $warehouse->code = $prefix . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }
         
}
