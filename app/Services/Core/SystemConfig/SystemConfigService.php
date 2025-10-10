<?php

namespace App\Services\Core\SystemConfig;

use App\Models\SystemConfig;
use App\Repositories\SystemConfig\SystemConfigRepository;
use App\Services\Core\SystemConfig\ConfigValidationService;
use App\Services\Core\SystemConfig\ConfigAuditService;
use App\Services\BaseService;
use App\Enums\ConfigGroup;
use App\Enums\ConfigType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class SystemConfigService extends BaseService
{
    protected ConfigValidationService $validationService;
    protected ConfigAuditService $auditService;
    protected ?array $oldConfig = null;

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
     * Hook method called when create operation succeeds
     * Clear cache and perform post-creation tasks
     */
    protected function onCreateSuccess(array $result, array $data): void
    {
        // Clear cache for this config group
        $this->clearCacheByGroup($result['group']);
        
        // Log audit
        $this->auditService->create([
            'config_key' => $result['key'],
            'old_value' => null,
            'new_value' => $result['value'] ?? null,
            'action' => 'created',
            'changed_by' => request()->user()?->id
        ]);
    }

    /**
     * Hook method called when update operation succeeds
     * Clear cache and perform post-update tasks
     */
    protected function onUpdateSuccess(array $result, $id, array $data): void
    {
        // Clear cache for this config group
        $this->clearCacheByGroup($result['group']);
        
        // Log audit
        $this->auditService->create([
            'config_key' => $result['key'],
            'old_value' => $this->oldConfig ? $this->oldConfig['value'] : null,
            'new_value' => $result['value'] ?? null,
            'action' => 'updated',
            'changed_by' => request()->user()?->id
        ]);
        
        // Clear old config
        $this->oldConfig = null;
    }

    /**
     * Hook method called when delete operation succeeds
     * Clear cache and perform post-deletion tasks
     */
    protected function onDeleteSuccess(?array $item, $id): void
    {
        if ($item) {
            // Clear cache for this config group
            $this->clearCacheByGroup($item['group']);
            
            // Log audit
            $this->auditService->create([
                'config_key' => $item['key'],
                'old_value' => $item['value'],
                'new_value' => null,
                'action' => 'deleted',
                'changed_by' => request()->user()?->id
            ]);
        }
    }

    /**
     * Hook method called when createOrUpdate operation succeeds
     * Clear cache and perform post-operation tasks
     */
    protected function onCreateOrUpdateSuccess(array $result, array $conditions, array $data): void
    {
        // Clear cache for this config group
        $this->clearCacheByGroup($result['group']);
        
        // Log audit
        $this->auditService->create([
            'config_key' => $result['key'],
            'old_value' => $this->oldConfig ? $this->oldConfig['value'] : null,
            'new_value' => $result['value'] ?? null,
            'action' => $this->oldConfig ? 'updated' : 'created',
            'changed_by' => request()->user()?->id
        ]);
        
        // Clear old config
        $this->oldConfig = null;
    }

    /**
     * Clear cache for a specific config group
     */
    private function clearCacheByGroup(string $group): void
    {
        // Clear cache for the specific group
        $cacheKey = "config_group_{$group}";
        Cache::forget($cacheKey);
        
        // Clear general config cache
        Cache::forget('all_configs');
        
        // Clear groups cache
        Cache::forget('system_config_groups');
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
            
            $result = $this->getBy($conditions);
            return $result ?? [];
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
            
            // Lấy value trực tiếp từ database, không qua accessor
            $rawValue = $config['value'];
            $type = $config['type'];
            $isEncrypted = $config['is_encrypted'] ?? false;
            
            // Decrypt nếu cần
            if ($isEncrypted && $rawValue) {
                try {
                    $rawValue = decrypt($rawValue);
                } catch (Exception $e) {
                    // Return original value if decryption fails
                }
            }
            
            // Parse theo type
            return $this->parseValueByType($rawValue, $type);
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
                $rawValue = $config['value'];
                $type = $config['type'];
                $isEncrypted = $config['is_encrypted'] ?? false;
                
                // Decrypt nếu cần
                if ($isEncrypted && $rawValue) {
                    try {
                        $rawValue = decrypt($rawValue);
                    } catch (Exception $e) {
                        // Return original value if decryption fails
                    }
                }
                
                $result[$config['key']] = $this->parseValueByType($rawValue, $type);
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
            $result = $this->getBy(['is_public' => true, 'status' => 'active']);
            return $result ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get all config groups with metadata
     */
    public function getGroups(): array
    {
        try {
            $cacheKey = 'system_config_groups';
            
            return Cache::remember($cacheKey, 600, function () {
                $configs = $this->getBy(['status' => 'active']);
                $configs = $configs ?? [];
                
                $groups = [];
                foreach ($configs as $config) {
                    $group = $config['group'];
                    
                    if (!isset($groups[$group])) {
                        $groups[$group] = [
                            'name' => $group,
                            'display_name' => ucfirst(str_replace('_', ' ', $group)),
                            'description' => $this->getGroupDescription($group),
                            'is_public' => $config['is_public'] ?? false,
                            'config_count' => 0,
                            'public_config_count' => 0
                        ];
                    }
                    
                    $groups[$group]['config_count']++;
                    if ($config['is_public']) {
                        $groups[$group]['public_config_count']++;
                    }
                }
                
                return array_values($groups);
            });
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get group description based on group name
     */
    private function getGroupDescription(string $group): string
    {
        $descriptions = [
            'general' => 'Cấu hình chung của hệ thống',
            'email' => 'Cấu hình email và SMTP',
            'payment' => 'Cấu hình thanh toán',
            'social' => 'Cấu hình mạng xã hội',
            'security' => 'Cấu hình bảo mật',
            'api' => 'Cấu hình API và tích hợp',
            'notification' => 'Cấu hình thông báo',
            'storage' => 'Cấu hình lưu trữ file',
            'cache' => 'Cấu hình cache',
            'database' => 'Cấu hình cơ sở dữ liệu',
        ];

        return $descriptions[$group] ?? "Nhóm cấu hình {$group}";
    }

    /**
     * Override BaseService create to handle validation
     */
    public function create($data): array
    {
        try {
            // Validate data
            $validatedData = $this->validationService->validate($data);
            
            // Create using BaseService method (hooks will handle audit)
            return parent::create($validatedData);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Override BaseService update to handle validation
     */
    public function update($id, $data): ?array
    {
        try {
            // Get old config for audit
            $oldConfig = $this->find($id);
            if (!$oldConfig) {
                return null;
            }
            
            // Store old config for hook
            $this->oldConfig = $oldConfig;
            
            // Validate data
            $validatedData = $this->validationService->validate($data);
            
            // Update using BaseService method (hooks will handle audit)
            return parent::update($id, $validatedData);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create or update config with validation
     */
    public function createOrUpdate(array $conditions, array $data, ?int $userId = null): array
    {
        try {
            // Validate data và lấy dữ liệu đã validate
            $validatedData = $this->validationService->validate($data);
            
            // Get old config for audit if updating
            $oldConfig = $this->findOneBy($conditions);
            
            // Store old config for hook
            $this->oldConfig = $oldConfig;
            
            // Create or update using BaseService method (hooks will handle audit)
            return parent::createOrUpdate($conditions, $validatedData);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Bulk update configs - Ultra-optimized version with transactions
     */
    public function bulkUpdate(array $configs, ?int $userId = null): array
    {
        try {
            return DB::transaction(function() use ($configs, $userId) {
                $results = [];
                $errors = [];
                $groupsToClear = [];
                $auditLogs = [];
                
                // Get all existing keys in one query
                $keys = array_column($configs, 'key');
                $existingConfigs = $this->repo->getBy(['key' => $keys]);
                $existingMap = collect($existingConfigs)->keyBy('key');
                
                // Separate create vs update
                $toCreate = [];
                $toUpdate = [];
                
                foreach ($configs as $config) {
                    if ($existingMap->has($config['key'])) {
                        $toUpdate[] = $config;
                    } else {
                        $toCreate[] = $config;
                    }
                }
                
                // Batch create
                if (!empty($toCreate)) {
                    foreach ($toCreate as $config) {
                        try {
                            $validatedData = $this->validationService->validate($config);
                            $result = $this->repo->create($validatedData);
                            $results[] = $result;
                            $groupsToClear[] = $result['group'];
                            
                            // Prepare audit log
                            $auditLogs[] = [
                                'config_key' => $result['key'],
                                'old_value' => null,
                                'new_value' => $result['value'],
                                'action' => 'created',
                                'changed_by' => $userId,
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                                'metadata' => json_encode([
                                    'config_key' => $result['key'],
                                    'value_changed' => true,
                                    'timestamp' => now()->toISOString(),
                                    'request_id' => request()->header('X-Request-ID'),
                                ]),
                                'created_at' => now()
                            ];
                        } catch (Exception $e) {
                            $errors[] = "Config {$config['key']}: " . $e->getMessage();
                        }
                    }
                }
                
                // Batch update
                if (!empty($toUpdate)) {
                    foreach ($toUpdate as $config) {
                        try {
                            $validatedData = $this->validationService->validate($config);
                            $existingConfig = $existingMap->get($config['key']);
                            
                            if ($existingConfig) {
                                $result = $this->repo->update($existingConfig['id'], $validatedData);
                                if ($result) {
                                    $results[] = $result;
                                    $groupsToClear[] = $result['group'];
                                    
                                    // Prepare audit log
                                    $auditLogs[] = [
                                        'config_key' => $result['key'],
                                        'old_value' => $existingConfig['value'],
                                        'new_value' => $result['value'],
                                        'action' => 'updated',
                                        'changed_by' => $userId,
                                        'ip_address' => request()->ip(),
                                        'user_agent' => request()->userAgent(),
                                        'metadata' => json_encode([
                                            'config_key' => $result['key'],
                                            'value_changed' => $existingConfig['value'] !== $result['value'],
                                            'timestamp' => now()->toISOString(),
                                            'request_id' => request()->header('X-Request-ID'),
                                        ]),
                                        'created_at' => now()
                                    ];
                                }
                            }
                        } catch (Exception $e) {
                            $errors[] = "Config {$config['key']}: " . $e->getMessage();
                        }
                    }
                }
                
                // Batch insert audit logs
                if (!empty($auditLogs)) {
                    DB::table('config_audit_logs')->insert($auditLogs);
                }
                
                // Clear cache for all affected groups (once)
                $uniqueGroups = array_unique($groupsToClear);
                foreach ($uniqueGroups as $group) {
                    $this->clearCacheByGroup($group);
                }
                
                if (empty($errors)) {
                    return [
                        'success' => true,
                        'data' => $results,
                        'message' => 'Tất cả cấu hình đã được cập nhật thành công',
                        'stats' => [
                            'total' => count($configs),
                            'created' => count($toCreate),
                            'updated' => count($toUpdate),
                            'groups_affected' => count($uniqueGroups)
                        ]
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Một số cấu hình có lỗi',
                        'data' => $results,
                        'errors' => $errors,
                        'stats' => [
                            'total' => count($configs),
                            'success' => count($results),
                            'failed' => count($errors),
                            'created' => count($toCreate),
                            'updated' => count($toUpdate)
                        ]
                    ];
                }
            });
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
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
     * Parse value by type
     */
    private function parseValueByType($value, string $type): mixed
    {
        // Simple type conversion
        switch ($type) {
            case 'integer':
                return is_numeric($value) ? (int) $value : $value;
            case 'float':
                return is_numeric($value) ? (float) $value : $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
                }
                return $value;
            case 'array':
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    return json_last_error() === JSON_ERROR_NONE ? $decoded : [$value];
                }
                return is_array($value) ? $value : [$value];
            default:
                return $value;
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
            try {
                $value = decrypt($value);
            } catch (Exception $e) {
                // Return original value if decryption fails
                $value = $config['value'];
            }
        }
        
        return $this->parseValueByType($value, $type);
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