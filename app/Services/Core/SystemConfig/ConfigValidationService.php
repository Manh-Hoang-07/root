<?php

namespace App\Services\Core\SystemConfig;

use App\Enums\ConfigType;
use App\Enums\ConfigGroup;
use Illuminate\Support\Facades\Validator;
use Exception;

class ConfigValidationService
{
    /**
     * Validate config data
     */
    public function validateConfigData(array $data): bool
    {
        $rules = $this->getValidationRules();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new Exception('Dữ liệu không hợp lệ: ' . implode(', ', $validator->errors()->all()));
        }

        $validatedData = $validator->validated();
        
        // Additional validation based on type
        $this->validateByType($validatedData);
        
        return true;
    }

    /**
     * Get validation rules
     */
    protected function getValidationRules(): array
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
            'default_value' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }

    /**
     * Validate by type
     */
    protected function validateByType(array $data): void
    {
        $type = ConfigType::from($data['type']);
        $value = $data['value'];

        if ($value === null) {
            return;
        }

        switch ($type) {
            case ConfigType::INTEGER:
                if (!is_numeric($value) || (int)$value != $value) {
                    throw new Exception('Giá trị phải là số nguyên');
                }
                break;

            case ConfigType::FLOAT:
                if (!is_numeric($value)) {
                    throw new Exception('Giá trị phải là số');
                }
                break;

            case ConfigType::BOOLEAN:
                if (!in_array($value, [true, false, 'true', 'false', '1', '0', 1, 0])) {
                    throw new Exception('Giá trị phải là boolean');
                }
                break;

            case ConfigType::JSON:
                if (is_string($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Giá trị phải là JSON hợp lệ');
                    }
                } elseif (!is_array($value)) {
                    throw new Exception('Giá trị phải là JSON hoặc array');
                }
                break;

            case ConfigType::ARRAY:
                if (!is_array($value) && !is_string($value)) {
                    throw new Exception('Giá trị phải là array hoặc string');
                }
                if (is_string($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Giá trị phải là array hợp lệ');
                    }
                }
                break;
        }
    }

    /**
     * Validate config key format
     */
    public function validateKey(string $key): bool
    {
        return preg_match('/^[a-zA-Z0-9._-]+$/', $key) === 1;
    }

    /**
     * Validate config value based on custom rules
     */
    public function validateValueByRules($value, array $rules): bool
    {
        if (empty($rules)) {
            return true;
        }

        foreach ($rules as $rule) {
            if (!$this->applyValidationRule($value, $rule)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply single validation rule
     */
    protected function applyValidationRule($value, string $rule): bool
    {
        $parts = explode(':', $rule);
        $ruleName = $parts[0];
        $ruleValue = $parts[1] ?? null;

        switch ($ruleName) {
            case 'min':
                return is_numeric($value) && $value >= (int)$ruleValue;
            case 'max':
                return is_numeric($value) && $value <= (int)$ruleValue;
            case 'min_length':
                return is_string($value) && strlen($value) >= (int)$ruleValue;
            case 'max_length':
                return is_string($value) && strlen($value) <= (int)$ruleValue;
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            case 'regex':
                return preg_match($ruleValue, $value) === 1;
            case 'in':
                $allowedValues = explode(',', $ruleValue);
                return in_array($value, $allowedValues);
            case 'not_in':
                $forbiddenValues = explode(',', $ruleValue);
                return !in_array($value, $forbiddenValues);
            default:
                return true;
        }
    }

    /**
     * Sanitize config value
     */
    public function sanitizeValue($value, ConfigType $type)
    {
        switch ($type) {
            case ConfigType::STRING:
                return trim((string)$value);
            case ConfigType::INTEGER:
                return (int)$value;
            case ConfigType::FLOAT:
                return (float)$value;
            case ConfigType::BOOLEAN:
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case ConfigType::JSON:
                return is_array($value) ? json_encode($value) : $value;
            case ConfigType::ARRAY:
                return is_array($value) ? $value : json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Validate group permissions
     */
    public function validateGroupAccess(string $group, int $userId): bool
    {
        // TODO: Implement permission checking
        // This will be implemented when we create the permission service
        return true;
    }

    /**
     * Validate key uniqueness
     */
    public function validateKeyUniqueness(string $key, ?int $excludeId = null): bool
    {
        $query = \App\Models\SystemConfig::where('key', $key);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }
}
