<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Role::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Role::class, 'parent_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(\App\Models\Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }
}
