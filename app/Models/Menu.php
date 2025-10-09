<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'route_name', 'icon', 'parent_id', 'sort_order'];

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
     * Helper: return roles as array
     */
    public function getRolesArrayAttribute(): array
    {
        if (!$this->roles) return [];
        $json = json_decode($this->roles, true);
        if (is_array($json)) return $json;
        return array_map('trim', explode(',', $this->roles));
    }
}
