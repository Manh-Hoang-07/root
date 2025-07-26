<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends SpatiePermission
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'parent_id',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }
} 