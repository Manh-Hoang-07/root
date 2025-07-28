<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'alt_text',
        'caption',
        'order',
        'imageable_type',
        'imageable_id',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // Relationships
    public function imageable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
} 