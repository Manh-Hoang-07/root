<?php

namespace App\Http\Requests\Admin\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class PermissionRequest extends FormRequest
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
        $permission = $this->route('permission');
        $permissionId = null;
        
        if ($permission) {
            $permissionId = is_object($permission) ? $permission->id : $permission;
        }

        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permissionId,
            'display_name' => 'nullable|string|max:255',
            'guard_name' => 'nullable|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:permissions,id',
                function ($attribute, $value, $fail) use ($permissionId) {
                    if ($value && $permissionId && $value == $permissionId) {
                        $fail('Quyền không thể chọn chính nó làm quyền cha.');
                    }
                }
            ],
            'status' => 'required|string|in:active,inactive',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Debug log
        Log::info('PermissionRequest validated data', [
            'original' => $this->all(),
            'validated' => $validated,
            'parent_id_original' => $this->input('parent_id'),
            'parent_id_validated' => $validated['parent_id'] ?? null
        ]);
        
        // Convert empty string and "null" to actual null for nullable fields
        $nullableFields = ['display_name', 'guard_name', 'parent_id'];
        foreach ($nullableFields as $field) {
            if (isset($validated[$field]) && ($validated[$field] === 'null' || $validated[$field] === '')) {
                $validated[$field] = null;
            }
        }
        
        // Debug log after conversion
        Log::info('PermissionRequest after conversion', [
            'final_validated' => $validated,
            'parent_id_final' => $validated['parent_id'] ?? null
        ]);
        
        return $validated;
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
