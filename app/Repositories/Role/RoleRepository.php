<?php
namespace App\Repositories\Role;

use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function model(): string
    {
        return Role::class;
    }

    public function find($id, array $relations = [], array $fields = ['*']): ?array
    {
        // Thêm permissions vào relations mặc định
        if (empty($relations)) {
            $relations = ['permissions'];
        } elseif (!in_array('permissions', $relations)) {
            $relations[] = 'permissions';
        }
        
        return parent::find($id, $relations, $fields);
    }
} 