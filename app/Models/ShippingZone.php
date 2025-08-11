<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $table = 'shipping_zones';
    protected $fillable = ['name', 'provinces', 'districts', 'base_fee', 'weight_fee', 'estimated_days', 'status'];
    
    protected $casts = [
        'provinces' => 'array',
        'districts' => 'array',
        'base_fee' => 'decimal:2',
        'weight_fee' => 'decimal:2',
    ];
}
