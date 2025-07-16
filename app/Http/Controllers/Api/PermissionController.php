<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PermissionService;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    // Lấy danh sách quyền (có phân trang)
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['search']);
        $permissions = $this->permissionService->list($filters, $perPage);
        return response()->json($permissions);
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
        $permission = $this->permissionService->create($data);
        return response()->json(['message' => 'Tạo quyền thành công', 'data' => $permission], 201);
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
        $permission = $this->permissionService->update($id, $data);
        return response()->json(['message' => 'Cập nhật quyền thành công', 'data' => $permission]);
    }

    // Xóa quyền
    public function destroy($id)
    {
        $this->permissionService->delete($id);
        return response()->json(['message' => 'Xóa quyền thành công']);
    }
}
