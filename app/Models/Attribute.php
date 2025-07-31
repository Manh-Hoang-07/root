<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status',
        'description',
        'is_required',
        'is_unique',
        'is_filterable',
        'is_searchable',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_unique' => 'boolean',
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
    ];

    /**
     * Get the attribute values for this attribute
     */
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Alias for attributeValues relationship
     */
    public function values()
    {
        return $this->attributeValues();
    }
} 