<?php
namespace App\Services\Permission;

use App\Repositories\Permission\PermissionRepository;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class PermissionService extends BaseService
{
    public function __construct(PermissionRepository $repo)
    {
        parent::__construct($repo);
    }

    public function update($id, $data)
    {
        // Debug log
        Log::info('PermissionService update', [
            'id' => $id,
            'data' => $data,
            'parent_id' => $data['parent_id'] ?? null
        ]);

        $permission = $this->repo->find($id);
        
        if (!$permission) {
            throw new ModelNotFoundException('Permission not found');
        }

        // Kiểm tra nếu permission có quyền con thì không cho phép sửa
        if ($permission->children()->exists()) {
            throw new \InvalidArgumentException('Không thể sửa quyền này vì nó có quyền con.');
        }

        $data = $this->handleImageUpload($data);
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        $permission = $this->repo->find($id);
        
        if (!$permission) {
            throw new ModelNotFoundException('Permission not found');
        }

        // Kiểm tra nếu permission có quyền con thì không cho phép xóa
        if ($permission->children()->exists()) {
            throw new \InvalidArgumentException('Không thể xóa quyền này vì nó có quyền con.');
        }

        return $this->repo->delete($id);
    }
} 