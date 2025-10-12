<?php

namespace App\Http\Requests\Core\SystemConfig;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ConfigType;
use App\Enums\ConfigGroup;

class SystemConfigRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255|regex:/^[a-zA-Z0-9._-]+$/',
            'value' => 'nullable',
            'type' => 'required|in:' . implode(',', array_column(ConfigType::getOptions(), 'value')),
            'group' => 'required|in:' . implode(',', array_column(ConfigGroup::getOptions(), 'value')),
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'is_encrypted' => 'boolean',
            'validation_rules' => 'nullable|array',
            'validation_rules.*' => 'string',
            'default_value' => 'nullable|string',
            'status' => 'in:active,inactive',
            'sort_order' => 'integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'key.required' => 'Khóa cấu hình là bắt buộc',
            'key.regex' => 'Khóa cấu hình chỉ được chứa chữ cái, số, dấu chấm, gạch dưới và gạch ngang',
            'key.max' => 'Khóa cấu hình không được vượt quá 255 ký tự',
            'type.required' => 'Loại dữ liệu là bắt buộc',
            'type.in' => 'Loại dữ liệu không hợp lệ',
            'group.required' => 'Nhóm cấu hình là bắt buộc',
            'group.in' => 'Nhóm cấu hình không hợp lệ',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'validation_rules.array' => 'Quy tắc validation phải là mảng',
            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên',
            'sort_order.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'key' => 'khóa cấu hình',
            'value' => 'giá trị',
            'type' => 'loại dữ liệu',
            'group' => 'nhóm cấu hình',
            'description' => 'mô tả',
            'is_public' => 'công khai',
            'is_encrypted' => 'mã hóa',
            'validation_rules' => 'quy tắc validation',
            'default_value' => 'giá trị mặc định',
            'status' => 'trạng thái',
            'sort_order' => 'thứ tự sắp xếp',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateValueByType($validator);
            $this->validateKeyUniqueness($validator);
        });
    }

    /**
     * Validate value based on type
     */
    protected function validateValueByType($validator): void
    {
        $type = $this->input('type');
        $value = $this->input('value');

        if ($value === null) {
            return;
        }

        switch ($type) {
            case ConfigType::INTEGER->value:
                if (!is_numeric($value) || (int)$value != $value) {
                    $validator->errors()->add('value', 'Giá trị phải là số nguyên');
                }
                break;

            case ConfigType::FLOAT->value:
                if (!is_numeric($value)) {
                    $validator->errors()->add('value', 'Giá trị phải là số');
                }
                break;

            case ConfigType::BOOLEAN->value:
                if (!in_array($value, [true, false, 'true', 'false', '1', '0', 1, 0])) {
                    $validator->errors()->add('value', 'Giá trị phải là boolean');
                }
                break;

            case ConfigType::JSON->value:
                if (is_string($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $validator->errors()->add('value', 'Giá trị phải là JSON hợp lệ');
                    }
                } elseif (!is_array($value)) {
                    $validator->errors()->add('value', 'Giá trị phải là JSON hoặc array');
                }
                break;

            case ConfigType::ARRAY->value:
                if (!is_array($value) && !is_string($value)) {
                    $validator->errors()->add('value', 'Giá trị phải là array hoặc string');
                }
                if (is_string($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $validator->errors()->add('value', 'Giá trị phải là array hợp lệ');
                    }
                }
                break;
        }
    }

    /**
     * Validate key uniqueness
     */
    protected function validateKeyUniqueness($validator): void
    {
        $key = $this->input('key');
        $configId = $this->route('system_config') ?? null;

        if (!$key) {
            return;
        }

        $query = \App\Models\SystemConfig::where('key', $key);
        
        if ($configId) {
            $query->where('id', '!=', $configId);
        }
        
        if ($query->exists()) {
            $validator->errors()->add('key', 'Khóa cấu hình đã tồn tại');
        }
    }
}
