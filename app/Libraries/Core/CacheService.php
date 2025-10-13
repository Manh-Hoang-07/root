<?php

namespace App\Libraries\Core;

use Illuminate\Support\Facades\Cache;

/**
 * Cache Service Library
 * 
 * Provides flexible caching functionality that can be used anywhere
 * with static methods for easy access.
 * 
 * @package App\Libraries\Core
 */
class CacheService
{
    /**
     * Get cached data if exists
     * 
     * @param string $cacheKey
     * @param string $cachePrefix
     * @return mixed|null
     */
    public static function get(string $cacheKey, string $cachePrefix = 'app')
    {
        return Cache::get($cachePrefix . ':' . $cacheKey);
    }

    /**
     * Store data in cache
     * 
     * @param string $cacheKey
     * @param mixed $data
     * @param int $cacheTtl
     * @param string $cachePrefix
     * @return void
     */
    public static function put(string $cacheKey, $data, int $cacheTtl = 3600, string $cachePrefix = 'app'): void
    {
        Cache::put($cachePrefix . ':' . $cacheKey, $data, $cacheTtl);
    }

    /**
     * Check if cache key exists
     * 
     * @param string $cacheKey
     * @param string $cachePrefix
     * @return bool
     */
    public static function has(string $cacheKey, string $cachePrefix = 'app'): bool
    {
        return Cache::has($cachePrefix . ':' . $cacheKey);
    }

    /**
     * Forget cache key
     * 
     * @param string $cacheKey
     * @param string $cachePrefix
     * @return void
     */
    public static function forget(string $cacheKey, string $cachePrefix = 'app'): void
    {
        Cache::forget($cachePrefix . ':' . $cacheKey);
    }

    /**
     * Generate a unique key for caching
     * 
     * @param array $filters
     * @param int $limit
     * @param string $context
     * @param bool $single
     * @param string|null $className
     * @return string
     */
    public static function generateKey(array $filters, int $limit, string $context, bool $single, ?string $className = null): string
    {
        $class = $className ?? 'default';
        $key = $class . ':' . $context . ':';
        $key .= md5(json_encode($filters) . $limit . $context . $single);
        return $key;
    }

    /**
     * Clear cache for specific context
     * 
     * @param string $context
     * @param string|null $className
     * @return void
     */
    public static function clear(string $context = 'all', ?string $className = null): void
    {
        // Note: This is a simplified cache clearing. In production, you might want to use Redis SCAN
        // or implement a more sophisticated cache clearing mechanism
        Cache::flush();
    }


}
