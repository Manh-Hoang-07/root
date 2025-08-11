<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'type',
        'discount_type',
        'discount_value',
        'min_order_value',
        'start_at',
        'end_at',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'discount_value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_products');
    }
}
