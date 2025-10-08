<?php

namespace App\Http\Requests\Core\SystemConfig;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ConfigType;
use App\Enums\ConfigGroup;

class BulkUpdateConfigRequest extends FormRequest
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
            'configs' => 'required|array|min:1|max:100',
            'configs.*.key' => 'required|string|max:255|regex:/^[a-zA-Z0-9._-]+$/',
            'configs.*.value' => 'nullable',
            'configs.*.type' => 'required|in:' . implode(',', array_column(ConfigType::getOptions(), 'value')),
            'configs.*.group' => 'required|in:' . implode(',', array_column(ConfigGroup::getOptions(), 'value')),
            'configs.*.description' => 'nullable|string|max:1000',
            'configs.*.is_public' => 'boolean',
            'configs.*.is_encrypted' => 'boolean',
            'configs.*.validation_rules' => 'nullable|array',
            'configs.*.validation_rules.*' => 'string',
            'configs.*.default_value' => 'nullable|string',
            'configs.*.status' => 'in:active,inactive',
            'configs.*.sort_order' => 'integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'configs.required' => 'Danh sách cấu hình là bắt buộc',
            'configs.array' => 'Danh sách cấu hình phải là mảng',
            'configs.min' => 'Phải có ít nhất 1 cấu hình',
            'configs.max' => 'Không được vượt quá 100 cấu hình',
            'configs.*.key.required' => 'Khóa cấu hình là bắt buộc',
            'configs.*.key.regex' => 'Khóa cấu hình chỉ được chứa chữ cái, số, dấu chấm, gạch dưới và gạch ngang',
            'configs.*.key.max' => 'Khóa cấu hình không được vượt quá 255 ký tự',
            'configs.*.type.required' => 'Loại dữ liệu là bắt buộc',
            'configs.*.type.in' => 'Loại dữ liệu không hợp lệ',
            'configs.*.group.required' => 'Nhóm cấu hình là bắt buộc',
            'configs.*.group.in' => 'Nhóm cấu hình không hợp lệ',
            'configs.*.description.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'configs.*.validation_rules.array' => 'Quy tắc validation phải là mảng',
            'configs.*.sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên',
            'configs.*.sort_order.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'configs' => 'danh sách cấu hình',
            'configs.*.key' => 'khóa cấu hình',
            'configs.*.value' => 'giá trị',
            'configs.*.type' => 'loại dữ liệu',
            'configs.*.group' => 'nhóm cấu hình',
            'configs.*.description' => 'mô tả',
            'configs.*.is_public' => 'công khai',
            'configs.*.is_encrypted' => 'mã hóa',
            'configs.*.validation_rules' => 'quy tắc validation',
            'configs.*.default_value' => 'giá trị mặc định',
            'configs.*.status' => 'trạng thái',
            'configs.*.sort_order' => 'thứ tự sắp xếp',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateConfigs($validator);
            $this->validateUniqueKeys($validator);
        });
    }

    /**
     * Validate each config
     */
    protected function validateConfigs($validator): void
    {
        $configs = $this->input('configs', []);

        foreach ($configs as $index => $config) {
            $this->validateConfigValue($validator, $config, $index);
        }
    }

    /**
     * Validate config value by type
     */
    protected function validateConfigValue($validator, array $config, int $index): void
    {
        $type = $config['type'] ?? null;
        $value = $config['value'] ?? null;

        if ($value === null) {
            return;
        }

        switch ($type) {
            case ConfigType::INTEGER->value:
                if (!is_numeric($value) || (int)$value != $value) {
                    $validator->errors()->add("configs.{$index}.value", 'Giá trị phải là số nguyên');
                }
                break;

            case ConfigType::FLOAT->value:
                if (!is_numeric($value)) {
                    $validator->errors()->add("configs.{$index}.value", 'Giá trị phải là số');
                }
                break;

            case ConfigType::BOOLEAN->value:
                if (!in_array($value, [true, false, 'true', 'false', '1', '0', 1, 0])) {
                    $validator->errors()->add("configs.{$index}.value", 'Giá trị phải là boolean');
                }
                break;

            case ConfigType::JSON->value:
                if (is_string($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $validator->errors()->add("configs.{$index}.value", 'Giá trị phải là JSON hợp lệ');
                    }
                } elseif (!is_array($value)) {
                    $validator->errors()->add("configs.{$index}.value", 'Giá trị phải là JSON hoặc array');
                }
                break;

            case ConfigType::ARRAY->value:
                if (!is_array($value) && !is_string($value)) {
                    $validator->errors()->add("configs.{$index}.value", 'Giá trị phải là array hoặc string');
                }
                if (is_string($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $validator->errors()->add("configs.{$index}.value", 'Giá trị phải là array hợp lệ');
                    }
                }
                break;
        }
    }

    /**
     * Validate unique keys within the request
     */
    protected function validateUniqueKeys($validator): void
    {
        $configs = $this->input('configs', []);
        $keys = array_column($configs, 'key');
        $duplicateKeys = array_diff_assoc($keys, array_unique($keys));

        if (!empty($duplicateKeys)) {
            foreach ($duplicateKeys as $index => $key) {
                $validator->errors()->add("configs.{$index}.key", 'Khóa cấu hình bị trùng lặp trong yêu cầu');
            }
        }
    }
}
