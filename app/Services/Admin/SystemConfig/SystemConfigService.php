<?php

namespace App\Services\Admin\SystemConfig;

use App\Models\SystemConfig;
use App\Repositories\SystemConfig\SystemConfigRepository;
use App\Services\BaseService;
use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SystemConfigService extends BaseService
{
    protected string $cachePrefix = 'system_config_';
    protected int $cacheTtl = 3600; // 1 giờ

    public function __construct(SystemConfigRepository $repository)
    {
        parent::__construct($repository);
    }

    // ==================== ADMIN METHODS ====================

    /**
     * Lấy config theo group với cache
     */
    public function getByGroup(string $group): array
    {
        $cacheKey = $this->cachePrefix . 'group_' . $group;
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($group) {
            return $this->repo->getByGroup($group);
        });
    }

    /**
     * Lấy config theo key với cache
     */
    public function getByKey(string $key, $default = null): mixed
    {
        $cacheKey = $this->cachePrefix . 'key_' . $key;
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($key, $default) {
            return $this->repo->getByKey($key, $default);
        });
    }

    /**
     * Lấy config theo group dạng array với cache
     */
    public function getGroupAsArray(string $group): array
    {
        $cacheKey = $this->cachePrefix . 'group_array_' . $group;
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($group) {
            return $this->repo->getGroupAsArray($group);
        });
    }

    /**
     * Clear cache cho key
     */
    public function clearKeyCache(string $key): void
    {
        Cache::forget($this->cachePrefix . 'key_' . $key);
    }

    /**
     * Clear cache cho group
     */
    public function clearGroupCache(string $group): void
    {
        $keys = [
            $this->cachePrefix . 'group_' . $group,
            $this->cachePrefix . 'group_array_' . $group,
        ];
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear tất cả cache
     */
    public function clearAllCache(): void
    {
        $groups = $this->repo->getGroups();
        
        foreach ($groups as $group) {
            $this->clearGroupCache($group);
        }
        
        // Clear other caches
        Cache::forget($this->cachePrefix . 'public');
        Cache::forget($this->cachePrefix . 'groups');
    }

    /**
     * Tạo config mới (Admin)
     */
    public function create($data): array
    {
        // Validate required fields
        $this->validateRequiredFields($data);

        // Set default values
        $data = $this->setDefaultValues($data);

        // Create config
        $config = $this->repo->create($data);

        // Clear cache
        $this->clearConfigCache($config->config_group);

        // Log activity
        Log::info('System config created', [
            'config_key' => $config->config_key,
            'config_group' => $config->config_group,
            'user_id' => Auth::check() ? Auth::id() : null
        ]);

        return $config;
    }

    /**
     * Cập nhật config (Admin)
     */
    public function update($id, $data): ?array
    {
        $config = $this->repo->find($id);
        
        if (!$config) {
            return null;
        }

        // Store old value for audit
        $oldValue = $config->config_value;

        // Validate required fields
        $this->validateRequiredFields($data, $config);

        // Update config
        $config = $this->repo->update($id, $data);

        // Clear cache
        $this->clearConfigCache($config->config_group);

        // Log activity
        Log::info('System config updated', [
            'config_key' => $config->config_key,
            'config_group' => $config->config_group,
            'old_value' => $oldValue,
            'new_value' => $config->config_value,
            'user_id' => Auth::check() ? Auth::id() : null
        ]);

        return $config;
    }

    /**
     * Xóa config (Admin)
     */
    public function delete($id): bool
    {
        $config = $this->repo->find($id);
        
        if (!$config) {
            return false;
        }

        $configGroup = $config->config_group;
        $configKey = $config->config_key;

        $result = $this->repo->delete($id);

        if ($result) {
            // Clear cache
            $this->clearConfigCache($configGroup);

            // Log activity
            Log::info('System config deleted', [
                'config_key' => $configKey,
                'config_group' => $configGroup,
                'user_id' => Auth::check() ? Auth::id() : null
            ]);
        }

        return $result;
    }


    /**
     * Cập nhật config theo group (Admin)
     */
    public function updateGroup(string $group, array $data): bool
    {
        $result = $this->repo->updateGroup($group, $data);

        if ($result) {
            // Clear cache
            $this->clearConfigCache($group);

            // Log activity
            Log::info('System config group updated', [
                'config_group' => $group,
                'updated_configs' => array_keys($data),
                'user_id' => Auth::check() ? Auth::id() : null
            ]);
        }

        return $result;
    }

    /**
     * Bulk update configs (Admin)
     */
    public function bulkUpdate(array $configs): int
    {
        $updated = $this->repo->bulkUpdate($configs);

        if ($updated > 0) {
            // Clear all cache
            $this->clearAllConfigCache();

            // Log activity
            Log::info('System configs bulk updated', [
                'updated_count' => $updated,
                'user_id' => Auth::check() ? Auth::id() : null
            ]);
        }

        return $updated;
    }

    /**
     * Bulk delete configs (Admin)
     */
    public function bulkDelete(array $ids): int
    {
        $deleted = $this->repo->bulkDelete($ids);

        if ($deleted > 0) {
            // Clear all cache
            $this->clearAllConfigCache();

            // Log activity
            Log::info('System configs bulk deleted', [
                'deleted_count' => $deleted,
                'deleted_ids' => $ids,
                'user_id' => Auth::check() ? Auth::id() : null
            ]);
        }

        return $deleted;
    }

    /**
     * Lấy danh sách groups (Admin)
     */
    public function getGroups(): array
    {
        return $this->repo->getGroups();
    }

    /**
     * Tìm kiếm config (Admin)
     */
    public function search(array $filters): array
    {
        return $this->repo->search($filters);
    }

    // ==================== API RESPONSE METHODS ====================

    /**
     * Lấy config theo key với API response
     */
    public function getByKeyResponse(string $key, $default = null): \Illuminate\Http\JsonResponse
    {
        try {
            $value = $this->getByKey($key, $default);
            return ApiResponse::success($value, 'Lấy cấu hình thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi lấy cấu hình: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật config theo key với API response
     */
    public function updateByKeyResponse(string $key, $value): \Illuminate\Http\JsonResponse
    {
        try {
            $config = $this->repo->getModel()
                ->where('config_key', $key)
                ->first();
                
            if (!$config) {
                return ApiResponse::error("Config key '{$key}' không tồn tại");
            }

            $config->update(['config_value' => $value]);
            
            // Clear cache
            $this->clearConfigCache($config->config_group);
            
            return ApiResponse::success($config->value, 'Cập nhật cấu hình thành công');
        } catch (\Exception $e) {
            return ApiResponse::error('Lỗi khi cập nhật cấu hình: ' . $e->getMessage());
        }
    }

    // ==================== CACHE METHODS ====================

    /**
     * Xóa cache cấu hình
     */
    public function clearCache(): bool
    {
        try {
            $this->clearAllConfigCache();
            return true;
        } catch (\Exception $e) {
            Log::error('Error clearing config cache: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear cache cho group
     */
    private function clearConfigCache(string $group): void
    {
        $this->clearGroupCache($group);
    }

    /**
     * Clear tất cả cache
     */
    private function clearAllConfigCache(): void
    {
        $groups = $this->getGroups();
        
        foreach ($groups as $group) {
            $this->clearConfigCache($group);
        }
    }

    // ==================== VALIDATION METHODS ====================

    /**
     * Validate required fields
     */
    private function validateRequiredFields(array $data, ?SystemConfig $config = null): void
    {
        $requiredFields = ['config_key', 'config_group', 'display_name'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Field {$field} is required");
            }
        }

        // Check unique key
        if ($config) {
            $existing = $this->repo->getModel()
                ->where('config_key', $data['config_key'])
                ->where('id', '!=', $config->id)
                ->first();
        } else {
            $existing = $this->repo->getModel()
                ->where('config_key', $data['config_key'])
                ->first();
        }

        if ($existing) {
            throw new \InvalidArgumentException("Config key {$data['config_key']} already exists");
        }
    }

    /**
     * Set default values
     */
    private function setDefaultValues(array $data): array
    {
        $defaults = [
            'config_type' => 'string',
            'is_public' => false,
            'is_required' => false,
            'sort_order' => 0,
            'status' => 'active'
        ];

        return array_merge($defaults, $data);
    }
}
