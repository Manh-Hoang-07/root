<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provinces',
        'districts',
        'base_fee',
        'weight_fee',
        'estimated_days',
        'status',
    ];

    protected $casts = [
        'provinces' => 'array',
        'districts' => 'array',
        'base_fee' => 'decimal:2',
        'weight_fee' => 'decimal:2',
        'estimated_days' => 'integer',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function calculateShippingFee($weight = 0)
    {
        return $this->base_fee + ($this->weight_fee * $weight);
    }
}
