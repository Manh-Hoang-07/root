<?php

namespace App\Http\Requests\Admin\Role;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roleRoute = $this->route('role');
        $roleId = is_object($roleRoute) ? $roleRoute->id : $roleRoute;

        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
            'display_name' => 'required|string|max:255',
            'guard_name' => 'sometimes|string|max:255',
            'parent_id' => 'nullable|exists:roles,id',
            'status' => 'sometimes|string|in:active,inactive',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên vai trò không được để trống.',
            'name.max' => 'Tên vai trò không được vượt quá :max ký tự.',
            'name.unique' => 'Tên vai trò đã tồn tại.',
            'display_name.required' => 'Tên hiển thị không được để trống.',
            'display_name.max' => 'Tên hiển thị không được vượt quá :max ký tự.',
            'parent_id.exists' => 'Vai trò cha không hợp lệ.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'permissions.array' => 'Danh sách quyền phải là mảng.',
            'permissions.*.exists' => 'Quyền không hợp lệ.',
        ];
    }
}
