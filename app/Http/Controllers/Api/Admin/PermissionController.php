<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\PermissionResource;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends BaseController
{
    public function __construct(PermissionService $permissionService)
    {
        parent::__construct($permissionService, PermissionResource::class);
    }

    // Lấy danh sách quyền (có phân trang)
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['search']);
        $permissions = $this->service->list($filters, $perPage);
        return PermissionResource::collection($permissions);
    }

    // Tạo quyền mới
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'display_name' => ['required', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ]);
        $data['guard_name'] = $data['guard_name'] ?? 'web';
        $permission = $this->service->create($data);
        return response()->json(['message' => 'Tạo quyền thành công', 'data' => new PermissionResource($permission)], 201);
    }

    // Cập nhật quyền
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('permissions', 'name')->ignore($id)
            ],
            'display_name' => ['required', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ]);
        $permission = $this->service->update($id, $data);
        return response()->json(['message' => 'Cập nhật quyền thành công', 'data' => new PermissionResource($permission)]);
    }

    // Xóa quyền
    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Xóa quyền thành công']);
    }
} 