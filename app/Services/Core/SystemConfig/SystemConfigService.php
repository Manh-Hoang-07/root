<?php

namespace App\Services\Core\SystemConfig;

use App\Models\SystemConfig;
use App\Repositories\SystemConfig\SystemConfigRepository;
use App\Services\Core\SystemConfig\ConfigValidationService;
use App\Services\Core\SystemConfig\ConfigAuditService;
use App\Services\BaseService;
use App\Enums\ConfigGroup;
use App\Enums\ConfigType;
use Exception;

class SystemConfigService extends BaseService
{
    protected ConfigValidationService $validationService;
    protected ConfigAuditService $auditService;

    public function __construct(
        SystemConfigRepository $repository,
        ConfigValidationService $validationService,
        ConfigAuditService $auditService
    ) {
        parent::__construct($repository);
        $this->validationService = $validationService;
        $this->auditService = $auditService;
    }

    /**
     * Get all config groups
     */
    public function getGroups(): array
    {
        return $this->repo->getGroups();
    }

    /**
     * Get configs by group
     */
    public function getByGroup(string $group, bool $publicOnly = false): array
    {
        try {
            $conditions = ['group' => $group, 'status' => 'active'];
            if ($publicOnly) {
                $conditions['is_public'] = true;
            }
            
            return $this->getBy($conditions);
        } catch (Exception $e) {
            throw new Exception("Failed to get configs by group: " . $e->getMessage());
        }
    }

    /**
     * Get config by key
     */
    public function getByKey(string $key, $default = null, bool $publicOnly = false): mixed
    {
        try {
            $conditions = ['key' => $key, 'status' => 'active'];
            if ($publicOnly) {
                $conditions['is_public'] = true;
            }
            
            $config = $this->findOneBy($conditions);
            
            if (!$config) {
                return $default;
            }
            
            return $this->parseConfigValue($config);
        } catch (Exception $e) {
            return $default;
        }
    }

    /**
     * Get multiple configs by keys
     */
    public function getByKeys(array $keys, bool $publicOnly = false): array
    {
        try {
            $conditions = ['key' => $keys, 'status' => 'active'];
            if ($publicOnly) {
                $conditions['is_public'] = true;
            }
            
            $configs = $this->getBy($conditions);
            $result = [];
            
            foreach ($configs as $config) {
                $result[$config['key']] = $this->parseConfigValue($config);
            }
            
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get all public configs
     */
    public function getPublicConfigs(): array
    {
        try {
            return $this->getBy(['is_public' => true, 'status' => 'active']);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Override BaseService create to handle audit logging
     */
    public function create($data): array
    {
        try {
            // Validate data
            $validatedData = $this->validationService->validate($data);
            
            // Create using BaseService method
            $result = parent::create($validatedData);
            
            // Log audit
            $this->auditService->create([
                'config_key' => $validatedData['key'],
                'old_value' => null,
                'new_value' => $result['value'] ?? null,
                'action' => 'created',
                'changed_by' => request()->user()?->id
            ]);
            
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Override BaseService update to handle audit logging
     */
    public function update($id, $data): ?array
    {
        try {
            // Get old config for audit
            $oldConfig = $this->find($id);
            if (!$oldConfig) {
                return null;
            }
            
            // Validate data
            $validatedData = $this->validationService->validate($data);
            
            // Update using BaseService method
            $result = parent::update($id, $validatedData);
            
            if ($result) {
                // Log audit
                $this->auditService->create([
                    'config_key' => $validatedData['key'],
                    'old_value' => $oldConfig['value'],
                    'new_value' => $result['value'] ?? null,
                    'action' => 'updated',
                    'changed_by' => request()->user()?->id
                ]);
            }
            
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create or update config with validation and audit
     */
    public function createOrUpdate(array $conditions, array $data, ?int $userId = null): array
    {
        try {
            // Validate data và lấy dữ liệu đã validate
            $validatedData = $this->validationService->validate($data);
            
            // Get old config for audit if updating
            $oldConfig = $this->findOneBy($conditions);
            
            // Create or update using BaseService method
            $result = parent::createOrUpdate($conditions, $validatedData);
            
            // Log audit
            $this->auditService->create([
                'config_key' => $validatedData['key'],
                'old_value' => $oldConfig ? $oldConfig['value'] : null,
                'new_value' => $result['value'] ?? null,
                'action' => $oldConfig ? 'updated' : 'created',
                'changed_by' => $userId
            ]);
            
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Bulk update configs
     */
    public function bulkUpdate(array $configs, ?int $userId = null): array
    {
        try {
            $results = [];
            $errors = [];
            
            foreach ($configs as $index => $config) {
                try {
                    $result = $this->createOrUpdate(['key' => $config['key']], $config, $userId);
                    $results[] = $result;
                } catch (Exception $e) {
                    $errors[] = "Config {$index}: " . $e->getMessage();
                }
            }
            
            
            if (empty($errors)) {
                return [
                    'success' => true,
                    'data' => $results,
                    'message' => 'Tất cả cấu hình đã được cập nhật'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Một số cấu hình có lỗi',
                    'data' => $results,
                    'errors' => $errors
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Override BaseService delete to handle audit logging
     */
    public function delete($id): bool
    {
        try {
            // Get config before deletion for audit
            $config = $this->find($id);
            if (!$config) {
                return false;
            }
            
            // Delete using BaseService method
            $deleted = parent::delete($id);
            
            if ($deleted) {
                // Log audit
                $this->auditService->create([
                    'config_key' => $config['key'],
                    'old_value' => $config['value'],
                    'new_value' => null,
                    'action' => 'deleted',
                    'changed_by' => request()->user()?->id
                ]);
            }
            
            return $deleted;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Parse config value based on type - simplified version
     */
    private function parseConfigValue(array $config): mixed
    {
        $value = $config['value'];
        $type = $config['type'];
        
        // Decrypt if needed
        if ($config['is_encrypted']) {
            $value = decrypt($value);
        }
        
        // Simple type conversion
        return match ($type) {
            ConfigType::INTEGER->value => (int) $value,
            ConfigType::FLOAT->value => (float) $value,
            ConfigType::BOOLEAN->value => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            ConfigType::JSON->value => is_string($value) ? json_decode($value, true) : $value,
            ConfigType::ARRAY->value => is_string($value) ? json_decode($value, true) ?: [$value] : (array) $value,
            default => $value
        };
    }

    /**
     * Clear all config cache
     */
    public function clearAllCache(): bool
    {
        try {
            // Clear Laravel cache
            \Illuminate\Support\Facades\Cache::forget('system_configs');
            \Illuminate\Support\Facades\Cache::forget('system_config_groups');
            
            // Clear any other config-related cache keys
            $cacheKeys = \Illuminate\Support\Facades\Cache::getStore()->getPrefix() . '*system_config*';
            \Illuminate\Support\Facades\Cache::flush();
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}