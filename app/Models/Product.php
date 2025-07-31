<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'image',
        'weight',
        'length',
        'width',
        'height',
        'brand_id',
        'status',
        'attributes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'float',
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
        'attributes' => 'array',
    ];

    // Relationships
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors
    public function getBrandNameAttribute()
    {
        return $this->brand ? $this->brand->name : null;
    }

    public function getCategoryNamesAttribute()
    {
        return $this->categories->pluck('name')->implode(', ');
    }

    public function getTotalQuantityAttribute()
    {
        return $this->inventory->sum('quantity');
    }

    public function getMainSkuAttribute()
    {
        return $this->variants->first() ? $this->variants->first()->sku : null;
    }

    /**
     * Set a product attribute
     */
    public function setProductAttribute($key, $value)
    {
        $attributes = $this->attributes ?? [];
        $attributes[$key] = $value;
        $this->attributes = $attributes;
    }

    /**
     * Get a product attribute
     */
    public function getProductAttribute($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Check if product has variants
     */
    public function hasVariants()
    {
        return $this->variants->count() > 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
