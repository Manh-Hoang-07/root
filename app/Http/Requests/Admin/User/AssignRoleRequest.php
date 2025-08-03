<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class AssignRoleRequest extends FormRequest
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
        return [
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert string "1,2,3" to array [1,2,3]
        if ($this->has('role_ids') && is_string($this->role_ids)) {
            $this->merge([
                'role_ids' => array_filter(array_map('trim', explode(',', $this->role_ids)))
            ]);
        }
        
        // Ensure role_ids is always an array
        if ($this->has('role_ids') && !is_array($this->role_ids)) {
            $this->merge([
                'role_ids' => [$this->role_ids]
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'role_ids.required' => 'Vui lòng chọn ít nhất một vai trò.',
            'role_ids.array' => 'Danh sách vai trò phải là mảng.',
            'role_ids.min' => 'Vui lòng chọn ít nhất một vai trò.',
            'role_ids.*.exists' => 'Vai trò không hợp lệ.',
        ];
    }
} 