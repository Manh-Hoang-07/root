<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'sku',
        'category_id',
        'brand',
        'weight',
        'dimensions',
        'images',
        'status',
        'featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'images' => 'array',
        'featured' => 'boolean',
    ];

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'product_inventory')
                    ->withPivot('quantity', 'reserved_quantity', 'available_quantity')
                    ->withTimestamps();
    }

    public function inventory()
    {
        return $this->hasMany(ProductInventory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAvailableQuantityAttribute()
    {
        return $this->inventory()->sum('available_quantity');
    }

    public function getTotalQuantityAttribute()
    {
        return $this->inventory()->sum('quantity');
    }
}
