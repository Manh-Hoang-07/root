<?php

namespace App\Http\Requests\Admin\Permission;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $permissionId = $this->route('permission') ? $this->route('permission')->id : null;

        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permissionId,
            'display_name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:permissions,id',
            'guard_name' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên quyền không được để trống.',
            'name.max' => 'Tên quyền không được vượt quá :max ký tự.',
            'name.unique' => 'Tên quyền đã tồn tại.',
            'display_name.required' => 'Tên hiển thị không được để trống.',
            'display_name.max' => 'Tên hiển thị không được vượt quá :max ký tự.',
            'parent_id.exists' => 'Quyền cha không hợp lệ.',
            'guard_name.max' => 'Guard name không được vượt quá :max ký tự.',
        ];
    }
}
