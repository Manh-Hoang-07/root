<?php

namespace App\Models;

use App\Enums\AttributeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_required',
        'is_variation',
        'is_filterable',
        'sort_order',
        'status',
        'created_user_id',
        'updated_user_id',
    ];

    protected $casts = [
        'type' => AttributeType::class,
        'is_required' => 'boolean',
        'is_variation' => 'boolean',
        'is_filterable' => 'boolean',
        'status' => 'string',
    ];

    // Relationships
    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
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
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVariation($query)
    {
        return $query->where('is_variation', true);
    }

    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
