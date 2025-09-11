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
    public function model()
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

    /**
     * Tìm kiếm config
     */
    public function search(array $filters): Collection
    {
        $query = $this->model->newQuery();

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

        return $query->ordered()->get();
    }

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

    /**
     * Bulk update configs
     */
    public function bulkUpdate(array $configs): int
    {
        $updated = 0;
        
        foreach ($configs as $config) {
            if (isset($config['id']) && isset($config['config_value'])) {
                $result = $this->model
                    ->where('id', $config['id'])
                    ->update(['config_value' => $config['config_value']]);
                    
                if ($result) {
                    $updated++;
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
}
