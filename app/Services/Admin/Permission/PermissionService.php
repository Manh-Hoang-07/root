<?php
namespace App\Services\Admin\Permission;

use App\Repositories\Permission\PermissionRepository;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionService extends BaseService
{
    public function __construct(PermissionRepository $repo)
    {
        parent::__construct($repo);
    }

    public function update($id, $data): ?array
    {
        $permission = $this->repo->find($id);
        if (!$permission) {
            throw new ModelNotFoundException('Permission not found');
        }
        // Kiểm tra nếu permission có quyền con thì không cho phép sửa
        $permissionModel = $this->repo->getModel()->find($permission['id']);
        if ($permissionModel->children()->exists()) {
            throw new \InvalidArgumentException('Không thể sửa quyền này vì nó có quyền con.');
        }
        return $this->repo->update($id, $data);
    }

    public function delete($id): bool
    {
        $permission = $this->repo->find($id);
        if (!$permission) {
            throw new ModelNotFoundException('Permission not found');
        }
        // Kiểm tra nếu permission có quyền con thì không cho phép xóa
        $permissionModel = $this->repo->getModel()->find($permission['id']);
        if ($permissionModel->children()->exists()) {
            throw new \InvalidArgumentException('Không thể xóa quyền này vì nó có quyền con.');
        }
        return $this->repo->delete($id);
    }
} 