<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'title',
        'api',
        'path',
        'icon',
        'parent_id',
        'sort_order',
        'permissions',
        'status',
        'created_user_id',
        'updated_user_id'
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->with('children')->orderBy('sort_order');
    }

    /**
     * Eager-load nested children recursively
     */
    protected $with = [];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * Helper: return permissions as array
     */
    public function getPermissionsArrayAttribute(): array
    {
        if (!$this->permissions) return [];
        $json = json_decode($this->permissions, true);
        if (is_array($json)) return $json;
        return array_map('trim', explode(',', $this->permissions));
    }
}
