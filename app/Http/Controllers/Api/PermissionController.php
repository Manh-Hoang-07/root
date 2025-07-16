<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    // Lấy danh sách quyền (có phân trang)
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $query = Permission::query();
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }
        $permissions = $query->orderBy('id', 'desc')->paginate($perPage);
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
        $permission = Permission::create($data);
        return response()->json(['message' => 'Tạo quyền thành công', 'data' => $permission], 201);
    }

    // Cập nhật quyền
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $data = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id)
            ],
            'display_name' => ['required', 'string', 'max:255'],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ]);
        $data['guard_name'] = $data['guard_name'] ?? $permission->guard_name;
        $permission->update($data);
        return response()->json(['message' => 'Cập nhật quyền thành công', 'data' => $permission]);
    }

    // Xóa quyền
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json(['message' => 'Xóa quyền thành công']);
    }
}
