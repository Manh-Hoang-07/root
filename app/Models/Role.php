<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\RoleStatus;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'parent_id',
        'status',
    ];

    protected $casts = [
        'status' => RoleStatus::class,
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
