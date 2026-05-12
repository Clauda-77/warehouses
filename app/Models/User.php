<?php


namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\UserRole;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'is_active',
        'primary_warehouse_id',
        'secondary_warehouse_id',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'role' => UserRole::class,
        'settings' => 'array',
    ];

    public function primaryWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'primary_warehouse_id');
    }

    public function secondaryWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'secondary_warehouse_id');
    }


    public function getAllowedWarehouses()
    {
        if ($this->role === UserRole::ADMIN) {
            return Warehouse::all();
        }

        $warehouses = collect();

        if ($this->primary_warehouse_id) {
            $warehouses->push($this->primaryWarehouse);
        }

        if ($this->secondary_warehouse_id) {
            $warehouses->push($this->secondaryWarehouse);
        }

        return $warehouses->filter()->unique('id');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isWarehouseKeeper(): bool
    {
        return $this->role === UserRole::WAREHOUSE_KEEPER;
    }

    public function canAccessWarehouse($warehouseId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($warehouseId, [
            $this->primary_warehouse_id,
            $this->secondary_warehouse_id
        ]);
    }
}
