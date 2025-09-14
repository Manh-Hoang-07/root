<?php

namespace App\Repositories\SystemConfig;

use App\Models\SystemConfig;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class SystemConfigRepository extends BaseRepository
{
    /**
     * Get the model instance
     */
    public function model(): string
    {
        return SystemConfig::class;
    }

    /**
     * Get the model instance
     */
    public function getModel(): SystemConfig
    {
        return $this->model;
    }

    // ==================== BASIC CRUD METHODS ====================

    /**
     * Lấy config theo group
     */
    public function getByGroup(string $group): Collection
    {
        return $this->model
            ->byGroup($group)
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Lấy config theo group dạng key-value array
     */
    public function getGroupAsArray(string $group): array
    {
        $configs = $this->getByGroup($group);
        $result = [];
        
        foreach ($configs as $config) {
            $result[$config->config_key] = $config->value;
        }
        
        return $result;
    }

    /**
     * Lấy config theo key
     */
    public function getByKey(string $key, $default = null)
    {
        $config = $this->model
            ->where('config_key', $key)
            ->active()
            ->first();
            
        return $config ? $config->value : $default;
    }

    /**
     * Lấy config theo nhiều keys
     */
    public function getByKeys(array $keys): Collection
    {
        return $this->model
            ->whereIn('config_key', $keys)
            ->active()
            ->get();
    }

    /**
     * Lấy config theo type
     */
    public function getByType(string $type): Collection
    {
        return $this->model
            ->where('config_type', $type)
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Lấy config public
     */
    public function getPublicConfigs(): Collection
    {
        return $this->model
            ->public()
            ->active()
            ->ordered()
            ->get();
    }

    // ==================== UPDATE METHODS ====================

    /**
     * Cập nhật config theo group
     */
    public function updateGroup(string $group, array $data): bool
    {
        $updated = 0;
        
        foreach ($data as $key => $value) {
            $result = $this->model
                ->where('config_key', $key)
                ->where('config_group', $group)
                ->update(['config_value' => $value]);
                
            if ($result) {
                $updated++;
            }
        }
        
        return $updated > 0;
    }

    /**
     * Tạo hoặc cập nhật config
     */
    public function createOrUpdate(string $key, array $data): SystemConfig
    {
        return $this->model->updateOrCreate(
            ['config_key' => $key],
            $data
        );
    }

    /**
     * Cập nhật config theo key
     */
    public function updateByKey(string $key, $value): bool
    {
        $config = $this->model->where('config_key', $key)->first();
        
        if (!$config) {
            return false;
        }

        return $config->update(['config_value' => $value]);
    }

    // ==================== SEARCH METHODS ====================


    /**
     * Lấy danh sách groups
     */
    public function getGroups(): array
    {
        return $this->model
            ->select('config_group')
            ->distinct()
            ->pluck('config_group')
            ->toArray();
    }

    // ==================== BULK OPERATIONS ====================

    /**
     * Bulk update configs
     */
    public function bulkUpdate(array $configs): int
    {
        $updated = 0;
        
        foreach ($configs as $config) {
            if (isset($config['id']) && isset($config['config_value'])) {
                $configModel = $this->model->find($config['id']);
                if ($configModel) {
                    $result = $configModel->update(['config_value' => $config['config_value']]);
                    if ($result) {
                        $updated++;
                    }
                }
            }
        }
        
        return $updated;
    }

    /**
     * Bulk delete configs
     */
    public function bulkDelete(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    // ==================== UTILITY METHODS ====================

    /**
     * Kiểm tra config key có tồn tại không
     */
    public function keyExists(string $key): bool
    {
        return $this->model->where('config_key', $key)->exists();
    }

    /**
     * Lấy config theo group và type
     */
    public function getByGroupAndType(string $group, string $type): Collection
    {
        return $this->model
            ->byGroup($group)
            ->where('config_type', $type)
            ->active()
            ->ordered()
            ->get();
    }

    /**
     * Lấy config theo group và is_public
     */
    public function getByGroupAndPublic(string $group, bool $isPublic = true): Collection
    {
        return $this->model
            ->byGroup($group)
            ->where('is_public', $isPublic)
            ->active()
            ->ordered()
            ->get();
    }
}
