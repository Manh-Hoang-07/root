<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'province',
        'phone',
        'email',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_inventory')
                    ->withPivot('quantity', 'reserved_quantity', 'available_quantity')
                    ->withTimestamps();
    }

    public function inventory()
    {
        return $this->hasMany(ProductInventory::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
