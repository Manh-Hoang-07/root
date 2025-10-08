<?php

namespace App\Services\Core\SystemConfig;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class ConfigCacheService
{
    protected $cachePrefix = 'system_config_';
    protected $defaultTtl = 3600; // 1 hour

    /**
     * Remember cache value
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        try {
            $cacheKey = $this->getCacheKey($key);
            
            return Cache::remember($cacheKey, $ttl, $callback);
        } catch (Exception $e) {
            Log::warning("Cache error for key {$key}: " . $e->getMessage());
            return $callback();
        }
    }

    /**
     * Get cache value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        try {
            $cacheKey = $this->getCacheKey($key);
            return Cache::get($cacheKey, $default);
        } catch (Exception $e) {
            Log::warning("Cache get error for key {$key}: " . $e->getMessage());
            return $default;
        }
    }

    /**
     * Set cache value
     */
    public function set(string $key, mixed $value, ?int $ttl = null): bool
    {
        try {
            $cacheKey = $this->getCacheKey($key);
            $ttl = $ttl ?? $this->defaultTtl;
            
            return Cache::put($cacheKey, $value, $ttl);
        } catch (Exception $e) {
            Log::warning("Cache set error for key {$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Forget cache value
     */
    public function forget(string $key): bool
    {
        try {
            $cacheKey = $this->getCacheKey($key);
            return Cache::forget($cacheKey);
        } catch (Exception $e) {
            Log::warning("Cache forget error for key {$key}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear config cache by key
     */
    public function clearConfigCache(string $configKey): bool
    {
        $patterns = [
            "config_key_{$configKey}",
            "config_key_{$configKey}_public",
        ];

        return $this->clearByPatterns($patterns);
    }

    /**
     * Clear group cache
     */
    public function clearGroupCache(string $group): bool
    {
        $patterns = [
            "config_group_{$group}",
            "config_group_{$group}_public",
        ];

        return $this->clearByPatterns($patterns);
    }

    /**
     * Clear all config cache
     */
    public function clearAllConfigCache(): bool
    {
        try {
            $patterns = [
                'config_*',
                'system_config_*',
            ];

            return $this->clearByPatterns($patterns);
        } catch (Exception $e) {
            Log::error("Error clearing all config cache: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear cache by patterns
     */
    protected function clearByPatterns(array $patterns): bool
    {
        try {
            foreach ($patterns as $pattern) {
                $this->clearByPattern($pattern);
            }
            return true;
        } catch (Exception $e) {
            Log::error("Error clearing cache by patterns: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear cache by pattern
     */
    protected function clearByPattern(string $pattern): bool
    {
        try {
            $cacheKey = $this->getCacheKey($pattern);
            
            // For Redis, we can use pattern matching
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys($cacheKey);
                
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } else {
                // For other cache drivers, we need to track keys manually
                // This is a simplified approach - in production, you might want to use a more sophisticated solution
                Cache::forget($cacheKey);
            }
            
            return true;
        } catch (Exception $e) {
            Log::warning("Cache pattern clear error for pattern {$pattern}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cache key with prefix
     */
    protected function getCacheKey(string $key): string
    {
        return $this->cachePrefix . $key;
    }

    /**
     * Warm up cache for frequently accessed configs
     */
    public function warmUpCache(): bool
    {
        try {
            // Warm up public configs
            $this->warmUpPublicConfigs();
            
            // Warm up group configs
            $this->warmUpGroupConfigs();
            
            return true;
        } catch (Exception $e) {
            Log::error("Cache warm up error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Warm up public configs
     */
    protected function warmUpPublicConfigs(): void
    {
        $cacheKey = 'config_public_all';
        $this->set($cacheKey, [], 3600);
    }

    /**
     * Warm up group configs
     */
    protected function warmUpGroupConfigs(): void
    {
        $groups = ['general', 'api', 'cache'];
        
        foreach ($groups as $group) {
            $cacheKey = "config_group_{$group}";
            $this->set($cacheKey, [], 3600);
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        try {
            $stats = [
                'driver' => Cache::getDefaultDriver(),
                'prefix' => $this->cachePrefix,
                'default_ttl' => $this->defaultTtl,
            ];

            // Add Redis-specific stats if using Redis
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $stats['redis_info'] = $redis->info();
            }

            return $stats;
        } catch (Exception $e) {
            Log::warning("Error getting cache stats: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if cache is working
     */
    public function isCacheWorking(): bool
    {
        try {
            $testKey = 'cache_test_' . time();
            $testValue = 'test_value';
            
            $this->set($testKey, $testValue, 60);
            $retrieved = $this->get($testKey);
            $this->forget($testKey);
            
            return $retrieved === $testValue;
        } catch (Exception $e) {
            return false;
        }
    }
}
