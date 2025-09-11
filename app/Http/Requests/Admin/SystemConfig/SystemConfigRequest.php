<?php

namespace App\Http\Requests\Admin\SystemConfig;

use Illuminate\Foundation\Http\FormRequest;

class SystemConfigRequest extends FormRequest
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
        $rules = [
            'config_key' => 'required|string|max:255|unique:system_configs,config_key',
            'config_value' => 'nullable|string',
            'config_type' => 'required|string|in:string,number,boolean,json,file,email,url',
            'config_group' => 'required|string|max:100',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'is_required' => 'boolean',
            'validation_rules' => 'nullable|array',
            'default_value' => 'nullable|string',
            'sort_order' => 'integer|min:0',
            'status' => 'string|in:active,inactive'
        ];

        // Update rules
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['config_key'] = 'required|string|max:255|unique:system_configs,config_key,' . $this->route('id');
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'config_key.required' => 'Khóa cấu hình là bắt buộc',
            'config_key.unique' => 'Khóa cấu hình đã tồn tại',
            'config_key.max' => 'Khóa cấu hình không được vượt quá 255 ký tự',
            'config_type.required' => 'Kiểu dữ liệu là bắt buộc',
            'config_type.in' => 'Kiểu dữ liệu không hợp lệ',
            'config_group.required' => 'Nhóm cấu hình là bắt buộc',
            'config_group.max' => 'Nhóm cấu hình không được vượt quá 100 ký tự',
            'display_name.required' => 'Tên hiển thị là bắt buộc',
            'display_name.max' => 'Tên hiển thị không được vượt quá 255 ký tự',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'is_public.boolean' => 'Trường public phải là true hoặc false',
            'is_required.boolean' => 'Trường required phải là true hoặc false',
            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên',
            'sort_order.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0',
            'status.in' => 'Trạng thái không hợp lệ'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'config_key' => 'khóa cấu hình',
            'config_value' => 'giá trị cấu hình',
            'config_type' => 'kiểu dữ liệu',
            'config_group' => 'nhóm cấu hình',
            'display_name' => 'tên hiển thị',
            'description' => 'mô tả',
            'is_public' => 'công khai',
            'is_required' => 'bắt buộc',
            'validation_rules' => 'quy tắc validation',
            'default_value' => 'giá trị mặc định',
            'sort_order' => 'thứ tự sắp xếp',
            'status' => 'trạng thái'
        ];
    }
}
