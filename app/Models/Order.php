<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'shipping_fee',
        'shipping_discount',
        'promotion_discount',
        'final_price',
        'shipping_address_id',
        'note',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'shipping_discount' => 'decimal:2',
        'promotion_discount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function shippingInfo()
    {
        return $this->hasOne(ShippingInfo::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            $order->final_price = $order->total_price + $order->shipping_fee - $order->shipping_discount - $order->promotion_discount;
        });
    }
}
