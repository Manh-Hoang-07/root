<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends BaseController
{
    public function __construct(RoleService $roleService)
    {
        parent::__construct($roleService, RoleResource::class);
    }

    // Lấy danh sách vai trò (có phân trang)
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['search']);
        $roles = $this->service->list($filters, $perPage);
        return RoleResource::collection($roles);
    }

    // Tạo vai trò mới
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'display_name' => ['required', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:roles,id'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
        $data['guard_name'] = $data['guard_name'] ?? 'web';
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        $role = $this->service->create($data);
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }
        $role->load('permissions');
        return response()->json(['message' => 'Tạo vai trò thành công', 'data' => new RoleResource($role)], 201);
    }

    // Cập nhật vai trò
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->ignore($id)
            ],
            'display_name' => ['required', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:roles,id'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        $role = $this->service->update($id, $data);
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }
        $role->load('permissions');
        return response()->json(['message' => 'Cập nhật vai trò thành công', 'data' => new RoleResource($role)]);
    }

    // Xóa vai trò
    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Xóa vai trò thành công']);
    }
}
