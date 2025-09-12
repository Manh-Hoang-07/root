<?php

namespace App\Helpers;

use App\Models\SystemConfig;
use Illuminate\Support\Facades\Cache;

class ConfigHelper
{
    /**
     * Lấy giá trị cấu hình theo key
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "config_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            return SystemConfig::getByKey($key, $default);
        });
    }

    /**
     * Lấy tất cả cấu hình theo group
     */
    public static function getGroup(string $group): array
    {
        $cacheKey = "config_group_{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            return SystemConfig::getGroupAsArray($group);
        });
    }

    /**
     * Lấy tất cả cấu hình public
     */
    public static function getPublicConfigs(): array
    {
        $cacheKey = "config_public_all";
        
        return Cache::remember($cacheKey, 3600, function () {
            return SystemConfig::where('is_public', true)
                ->where('status', 'active')
                ->get()
                ->pluck('value', 'config_key')
                ->toArray();
        });
    }

    /**
     * Lấy cấu hình theo nhiều keys
     */
    public static function getMultiple(array $keys, $default = null): array
    {
        $result = [];
        
        foreach ($keys as $key) {
            $result[$key] = self::get($key, $default);
        }
        
        return $result;
    }

    /**
     * Lấy tất cả cấu hình theo danh sách groups
     */
    public static function getMultipleGroups(array $groups): array
    {
        $result = [];
        
        foreach ($groups as $group) {
            $result[$group] = self::getGroup($group);
        }
        
        return $result;
    }

    /**
     * Xóa cache cho một key
     */
    public static function clearCache(string $key): void
    {
        Cache::forget("config_{$key}");
    }

    /**
     * Xóa cache cho một group
     */
    public static function clearGroupCache(string $group): void
    {
        Cache::forget("config_group_{$group}");
    }

    /**
     * Xóa tất cả cache cấu hình
     */
    public static function clearAllCache(): void
    {
        Cache::forget("config_public_all");
    }
}
