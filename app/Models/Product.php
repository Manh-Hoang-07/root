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
        'code',
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

    // Tối ưu: Thêm appends để tránh accessor overhead
    protected $appends = [];

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

    // Tối ưu: Giảm accessor overhead bằng cách chỉ tính khi cần
    public function getBrandNameAttribute()
    {
        // Chỉ tính khi có brand relation và chưa có brand_name
        if ($this->relationLoaded('brand') && !isset($this->attributes['brand_name'])) {
            $this->attributes['brand_name'] = $this->brand ? $this->brand->name : null;
        }
        return $this->attributes['brand_name'] ?? null;
    }

    public function getCategoryNamesAttribute()
    {
        // Chỉ tính khi có categories relation và chưa có category_names
        if ($this->relationLoaded('categories') && !isset($this->attributes['category_names'])) {
            $this->attributes['category_names'] = $this->categories->pluck('name')->implode(', ');
        }
        return $this->attributes['category_names'] ?? null;
    }

    public function getTotalQuantityAttribute()
    {
        // Chỉ tính khi có inventory relation và chưa có total_quantity
        if ($this->relationLoaded('inventory') && !isset($this->attributes['total_quantity'])) {
            $this->attributes['total_quantity'] = $this->inventory->sum('quantity');
        }
        return $this->attributes['total_quantity'] ?? 0;
    }

    public function getMainSkuAttribute()
    {
        // Chỉ tính khi có variants relation và chưa có main_sku
        if ($this->relationLoaded('variants') && !isset($this->attributes['main_sku'])) {
            $this->attributes['main_sku'] = $this->variants->first() ? $this->variants->first()->sku : null;
        }
        return $this->attributes['main_sku'] ?? null;
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
     * Tối ưu: Check if product has variants - cache result
     */
    public function hasVariants()
    {
        if ($this->relationLoaded('variants')) {
            return $this->variants->count() > 0;
        }
        
        // Cache kết quả để tránh query nhiều lần
        $cacheKey = "product_has_variants_{$this->id}";
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () {
            return $this->variants()->count() > 0;
        });
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

    /**
     * Tối ưu: Scope để load relations hiệu quả
     */
    public function scopeWithOptimizedRelations($query)
    {
        return $query->with([
            'brand:id,name',
            'categories:id,name',
            'images:id,imageable_id,url'
        ]);
    }
}
