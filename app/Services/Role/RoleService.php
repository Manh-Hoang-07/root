<?php
namespace App\Services\Role;

use App\Repositories\Role\RoleRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Log;

class RoleService extends BaseService
{
    public function __construct(RoleRepository $repo)
    {
        parent::__construct($repo);
    }

    public function create($data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        

        
        $role = parent::create($data);
        
        if (!empty($permissions)) {
            // Đảm bảo permissions là array of integers
            $permissionIds = array_map('intval', (array) $permissions);

            $role->permissions()->sync($permissionIds);
        }
        
        return $role;
    }

    public function update($id, $data)
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        

        
        $role = parent::update($id, $data);
        
        // Đảm bảo permissions là array of integers
        $permissionIds = array_map('intval', (array) $permissions);

        $role->permissions()->sync($permissionIds);
        
        return $role;
    }
} 