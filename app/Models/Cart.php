<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

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
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
    public function getFormattedPriceAttribute()
    {
        $price = $this->variant
            ? ($this->variant->sale_price ?? $this->variant->price)
            : ($this->product->sale_price ?? $this->product->price);
        return number_format($price, 0, ',', '.') . ' VND';
    }

    public function getFormattedTotalPriceAttribute()
    {
        $price = $this->variant
            ? ($this->variant->sale_price ?? $this->variant->price)
            : ($this->product->sale_price ?? $this->product->price);
        $total = $price * $this->quantity;
        return number_format($total, 0, ',', '.') . ' VND';
    }
}
