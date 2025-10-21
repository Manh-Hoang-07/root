<?php

namespace App\Repositories\SystemConfig;

use App\Repositories\BaseRepository;
use App\Models\SystemConfig;
use App\Enums\ConfigGroup;

class SystemConfigRepository extends BaseRepository
{
    /**
     * Get the model class name
     */
    public function model(): string
    {
        return SystemConfig::class;
    }


    /**
     * Get config groups
     */
    public function getGroups(): array
    {
        return ConfigGroup::getOptions();
    }


    /**
     * Bulk update configs
     */
    public function bulkUpdate(array $configs): array
    {
        $results = [];
        
        foreach ($configs as $config) {
            $key = $config['key'];
            unset($config['key']); // Remove key from data
            
            $result = $this->createOrUpdate(['key' => $key], $config);
            $results[] = $result;
        }
        
        return $results;
    }


}