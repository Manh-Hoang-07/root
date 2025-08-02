<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockSummary extends Model
{
    protected $table = 'product_stock_summary';
    
    protected $fillable = [
        'product_id',
        'total_quantity',
        'warehouse_count',
        'low_stock_count',
        'last_updated_at'
    ];

    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
} 