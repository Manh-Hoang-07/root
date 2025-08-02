<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait Cacheable
{
    /**
     * Get cache key for model
     */
    public function getCacheKey($suffix = '')
    {
        $key = strtolower(class_basename($this)) . '_' . $this->getKey();
        if ($suffix) {
            $key .= '_' . $suffix;
        }
        return $key;
    }
    
    /**
     * Get cache key for model class
     */
    public static function getClassCacheKey($suffix = '')
    {
        $key = strtolower(class_basename(new static)) . '_class';
        if ($suffix) {
            $key .= '_' . $suffix;
        }
        return $key;
    }
    
    /**
     * Clear cache for this model
     */
    public function clearCache()
    {
        $pattern = strtolower(class_basename($this)) . '_' . $this->getKey() . '_*';
        $this->clearCacheByPattern($pattern);
        
        // Also clear class-level cache
        $classPattern = strtolower(class_basename($this)) . '_class_*';
        $this->clearCacheByPattern($classPattern);
        
        Log::info('Model cache cleared', [
            'model' => static::class,
            'id' => $this->getKey(),
            'pattern' => $pattern
        ]);
    }
    
    /**
     * Clear cache by pattern
     */
    protected function clearCacheByPattern($pattern)
    {
        // Clear specific cache keys
        $keys = Cache::get($pattern) ?: [];
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        // Also clear by pattern (if using Redis)
        if (config('cache.default') === 'redis') {
            try {
                $redis = Cache::getRedis();
                $keys = $redis->keys($pattern);
                foreach ($keys as $key) {
                    $redis->del($key);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to clear Redis cache by pattern', [
                    'pattern' => $pattern,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Boot the trait
     */
    protected static function bootCacheable()
    {
        static::created(function ($model) {
            $model->clearCache();
        });
        
        static::updated(function ($model) {
            $model->clearCache();
        });
        
        static::deleted(function ($model) {
            $model->clearCache();
        });
    }
} 