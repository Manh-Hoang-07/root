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
     * Create or update config
     */
    public function createOrUpdateConfig(array $data, ?int $userId = null): array
    {
        try {
            // Validate data
            $this->validationService->validateConfigData($data);
            
            // Create or update using BaseService method
            $result = $this->createOrUpdate(['key' => $data['key']], $data);
            
            // Log audit
            $this->auditService->logConfigChange(
                $data['key'],
                null,
                $result['value'] ?? null,
                'updated',
                $userId
            );
            
            
            return [
                'success' => true,
                'data' => $result,
                'message' => 'Cấu hình đã được cập nhật thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
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
                    $this->validationService->validateConfigData($config);
                    $result = $this->createOrUpdate(['key' => $config['key']], $config);
                    $results[] = $result;
                    
                    // Log audit
                    $this->auditService->logConfigChange(
                        $config['key'],
                        null,
                        $result['value'] ?? null,
                        'updated',
                        $userId
                    );
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
     * Delete config by key
     */
    public function deleteByKey(string $key, ?int $userId = null): array
    {
        try {
            $config = $this->findOneBy(['key' => $key, 'status' => 'active']);
            if (!$config) {
                return [
                    'success' => false,
                    'message' => 'Cấu hình không tồn tại'
                ];
            }
            
            $deleted = $this->delete($config['id']);
            
            if ($deleted) {
                // Log audit
                $this->auditService->logConfigChange(
                    $key,
                    $config['value'],
                    null,
                    'deleted',
                    $userId
                );
                
                
                return [
                    'success' => true,
                    'message' => 'Cấu hình đã được xóa thành công'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Không thể xóa cấu hình'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }





    /**
     * Parse config value based on type
     */
    private function parseConfigValue(array $config): mixed
    {
        $value = $config['value'];
        $type = $config['type'];
        
        if ($config['is_encrypted']) {
            $value = decrypt($value);
        }
        
        switch ($type) {
            case ConfigType::INTEGER->value:
                return (int) $value;
                
            case ConfigType::FLOAT->value:
                return (float) $value;
                
            case ConfigType::BOOLEAN->value:
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
                
            case ConfigType::JSON->value:
                return is_string($value) ? json_decode($value, true) : $value;
                
            case ConfigType::ARRAY->value:
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    return is_array($decoded) ? $decoded : [$value];
                }
                return is_array($value) ? $value : [$value];
                
            default:
                return $value;
        }
    }
}