<?php

namespace App\Services\SystemConfig;

use App\Models\SystemConfig;
use App\Repositories\SystemConfig\SystemConfigRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SystemConfigService
{
    protected $repository;

    public function __construct(SystemConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lấy danh sách config với pagination
     */
    public function list(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*'])
    {
        $query = $this->repository->getModel()->newQuery();

        // Apply filters
        if (isset($filters['group'])) {
            $query->byGroup($filters['group']);
        }

        if (isset($filters['type'])) {
            $query->where('config_type', $filters['type']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', $filters['is_public']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('config_key', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply relations
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Apply fields
        if (!empty($fields) && !in_array('*', $fields)) {
            $query->select($fields);
        }

        // Order by
        $query->ordered();

        return $query->paginate($perPage);
    }

    /**
     * Lấy config theo ID
     */
    public function find($id, array $relations = [], array $fields = ['*'])
    {
        $query = $this->repository->getModel()->newQuery();

        if (!empty($relations)) {
            $query->with($relations);
        }

        if (!empty($fields) && !in_array('*', $fields)) {
            $query->select($fields);
        }

        return $query->find($id);
    }

    /**
     * Tạo config mới
     */
    public function create(array $data): SystemConfig
    {
        // Validate required fields
        $this->validateRequiredFields($data);

        // Set default values
        $data = $this->setDefaultValues($data);

        // Create config
        $config = $this->repository->create($data);

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
     * Cập nhật config
     */
    public function update($id, array $data): ?SystemConfig
    {
        $config = $this->repository->find($id);
        
        if (!$config) {
            return null;
        }

        // Store old value for audit
        $oldValue = $config->config_value;

        // Validate required fields
        $this->validateRequiredFields($data, $config);

        // Update config
        $config = $this->repository->update($id, $data);

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
     * Xóa config
     */
    public function delete($id): bool
    {
        $config = $this->repository->find($id);
        
        if (!$config) {
            return false;
        }

        $configGroup = $config->config_group;
        $configKey = $config->config_key;

        $result = $this->repository->delete($id);

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
     * Lấy config theo group
     */
    public function getByGroup(string $group): Collection
    {
        $cacheKey = "system_configs_group_{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            return $this->repository->getByGroup($group);
        });
    }

    /**
     * Lấy config theo group dạng array
     */
    public function getGroupAsArray(string $group): array
    {
        $cacheKey = "system_configs_group_array_{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            return $this->repository->getGroupAsArray($group);
        });
    }

    /**
     * Lấy config theo key
     */
    public function getByKey(string $key, $default = null)
    {
        $cacheKey = "system_config_key_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            return $this->repository->getByKey($key, $default);
        });
    }

    /**
     * Cập nhật config theo group
     */
    public function updateGroup(string $group, array $data): bool
    {
        $result = $this->repository->updateGroup($group, $data);

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
     * Bulk update configs
     */
    public function bulkUpdate(array $configs): int
    {
        $updated = $this->repository->bulkUpdate($configs);

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
     * Bulk delete configs
     */
    public function bulkDelete(array $ids): int
    {
        $deleted = $this->repository->bulkDelete($ids);

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
     * Lấy danh sách groups
     */
    public function getGroups(): array
    {
        return $this->repository->getGroups();
    }

    /**
     * Tìm kiếm config
     */
    public function search(array $filters): Collection
    {
        return $this->repository->search($filters);
    }

    /**
     * Clear cache cho group
     */
    private function clearConfigCache(string $group): void
    {
        Cache::forget("system_configs_group_{$group}");
        Cache::forget("system_configs_group_array_{$group}");
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
            $existing = $this->repository->getModel()
                ->where('config_key', $data['config_key'])
                ->where('id', '!=', $config->id)
                ->first();
        } else {
            $existing = $this->repository->getModel()
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
