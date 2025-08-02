<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'batch_no',
        'lot_no',
        'expiry_date',
        'reserved_quantity',
        'available_quantity',
        'cost_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'available_quantity' => 'integer',
        'expiry_date' => 'date',
        'cost_price' => 'decimal:2',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Scopes
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
                    ->where('expiry_date', '>', Carbon::now())
                    ->where('available_quantity', '>', 0);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::now())
                    ->where('available_quantity', '>', 0);
    }

    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('available_quantity', '<=', $threshold)
                    ->where('available_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('available_quantity', '<=', 0);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->whereHas('product', function ($q) use ($brandId) {
            $q->where('brand_id', $brandId);
        });
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->whereHas('product.categories', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        });
    }

    // Accessors & Mutators
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute()
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getStockStatusAttribute()
    {
        if ($this->available_quantity <= 0) return 'out_of_stock';
        if ($this->available_quantity <= 10) return 'low_stock';
        return 'in_stock';
    }

    public function getStockStatusTextAttribute()
    {
        switch ($this->stock_status) {
            case 'out_of_stock':
                return 'Hết hàng';
            case 'low_stock':
                return 'Sắp hết';
            default:
                return 'Còn hàng';
        }
    }

    public function getStockStatusClassAttribute()
    {
        switch ($this->stock_status) {
            case 'out_of_stock':
                return 'bg-red-100 text-red-800';
            case 'low_stock':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-green-100 text-green-800';
        }
    }

    public function getExpiryStatusClassAttribute()
    {
        if (!$this->expiry_date) return 'bg-gray-100 text-gray-800';
        
        $daysLeft = $this->expiry_date->diffInDays(now(), false);
        
        if ($daysLeft < 0) return 'bg-red-100 text-red-800'; // Hết hạn
        if ($daysLeft <= 30) return 'bg-yellow-100 text-yellow-800'; // Sắp hết hạn
        return 'bg-green-100 text-green-800'; // Còn hạn
    }

    // Boot method để tự động tính available_quantity
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($inventory) {
            // Tự động tính available_quantity nếu không được set
            if (!isset($inventory->available_quantity)) {
                $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
            }
        });
    }
}
