<?php
namespace App\Services\Admin\Permission;

use App\Repositories\Permission\PermissionRepository;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionService extends BaseService
{
    /**
     * @var PermissionRepository
     */
    protected $repo;

    public function __construct(PermissionRepository $repo)
    {
        parent::__construct($repo);
    }

    public function update($id, $data): array
    {
        $permission = $this->repo->find($id);
        if (!$permission) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy quyền',
                'data' => null
            ];
        }
        // Kiểm tra nếu permission có quyền con thì không cho phép sửa
        $permissionModel = $this->repo->getModel()->find($permission['id']);
        if ($permissionModel->children()->exists()) {
            return [
                'success' => false,
                'message' => 'Không thể sửa quyền này vì nó có quyền con.',
                'data' => null
            ];
        }
        return $this->repo->update($id, $data);
    }

    public function delete($id): array
    {
        $permission = $this->repo->find($id);
        if (!$permission) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy quyền',
                'data' => null
            ];
        }
        // Kiểm tra nếu permission có quyền con thì không cho phép xóa
        $permissionModel = $this->repo->getModel()->find($permission['id']);
        if ($permissionModel->children()->exists()) {
            return [
                'success' => false,
                'message' => 'Không thể xóa quyền này vì nó có quyền con.',
                'data' => null
            ];
        }
        $result = $this->repo->delete($id);
        
        return [
            'success' => $result,
            'message' => $result ? 'Xóa quyền thành công' : 'Xóa quyền thất bại',
            'data' => null
        ];
    }
} 