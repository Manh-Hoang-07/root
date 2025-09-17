<?php
namespace App\Services\Admin\Role;

use App\Repositories\Role\RoleRepository;
use App\Services\BaseService;

class RoleService extends BaseService
{
    public function __construct(RoleRepository $repo)
    {
        parent::__construct($repo);
    }

    public function create($data): array
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        $role = parent::create($data);
        if (!empty($permissions)) {
            // Đảm bảo permissions là array of integers
            $permissionIds = array_map('intval', (array) $permissions);
            $roleModel = $this->repo->getModel()->find($role['id']);
            $roleModel->permissions()->sync($permissionIds);
        }
        return $role;
    }

    public function update($id, $data): ?array
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        $role = parent::update($id, $data);
        if (!$role) {
            return null;
        }
        // Đảm bảo permissions là array of integers
        $permissionIds = array_map('intval', (array) $permissions);
        $roleModel = $this->repo->getModel()->find($role['id']);
        $roleModel->permissions()->sync($permissionIds);
        return $role;
    }
} 