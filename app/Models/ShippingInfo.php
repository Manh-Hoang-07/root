<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tracking_number',
        'shipping_company',
        'shipping_method',
        'estimated_delivery',
        'actual_delivery',
        'shipping_cost',
    ];

    protected $casts = [
        'estimated_delivery' => 'date',
        'actual_delivery' => 'date',
        'shipping_cost' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
