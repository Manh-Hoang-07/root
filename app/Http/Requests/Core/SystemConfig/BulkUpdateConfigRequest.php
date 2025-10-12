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
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateUniqueKeys($validator);
        });
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
