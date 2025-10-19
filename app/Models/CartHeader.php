<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartHeader extends Model
{
    use HasFactory;

    protected $table = 'cart_headers';
    
    protected $fillable = [
        'id',
        'user_id',
        'session_id',
        'currency',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'coupon_code',
        'created_user_id',
        'updated_user_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(Cart::class, 'cart_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdUser()
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function updatedUser()
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeForGuest($query, $sessionId)
    {
        return $query->where('session_id', $sessionId)->whereNull('user_id');
    }

    // Accessors & Mutators
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 0, ',', '.') . ' ' . $this->currency;
    }

    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->total_amount, 0, ',', '.') . ' ' . $this->currency;
    }

    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }
}