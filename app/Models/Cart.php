<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'created_user_id',
        'updated_user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function updatedUser(): BelongsTo
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
    public function getItemPriceAttribute()
    {
        if ($this->variant) {
            return $this->variant->final_price;
        }
        return $this->product->final_price;
    }

    public function getItemTotalAttribute()
    {
        return $this->item_price * $this->quantity;
    }

    public function getItemNameAttribute()
    {
        if ($this->variant) {
            return $this->product->name . ' - ' . $this->variant->name;
        }
        return $this->product->name;
    }

    public function getItemSkuAttribute()
    {
        if ($this->variant) {
            return $this->variant->sku;
        }
        return $this->product->sku;
    }
}
