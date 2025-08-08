<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Cache;

/**
 * Cache Service Library
 * 
 * Provides flexible caching functionality that can be used anywhere
 * without requiring traits or inheritance.
 * 
 * @package App\Libraries
 */
class CacheService
{
    /** @var bool Enable caching for responses */
    private bool $enableCaching;
    
    /** @var int Cache TTL in seconds */
    private int $cacheTtl;
    
    /** @var string Cache prefix for namespacing */
    private string $cachePrefix;

    /**
     * Constructor
     * 
     * @param bool $enableCaching
     * @param int $cacheTtl
     * @param string $cachePrefix
     */
    public function __construct(bool $enableCaching = false, int $cacheTtl = 300, string $cachePrefix = 'app')
    {
        $this->enableCaching = $enableCaching;
        $this->cacheTtl = $cacheTtl;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Check if caching should be applied
     * 
     * @return bool
     */
    public function shouldCache(): bool
    {
        return $this->enableCaching && $this->cacheTtl > 0;
    }

    /**
     * Get cached data if exists
     * 
     * @param string $cacheKey
     * @return mixed|null
     */
    public function get(string $cacheKey)
    {
        if (!$this->shouldCache()) {
            return null;
        }

        return Cache::get($this->getFullKey($cacheKey));
    }

    /**
     * Store data in cache
     * 
     * @param string $cacheKey
     * @param mixed $data
     * @return void
     */
    public function put(string $cacheKey, $data): void
    {
        if (!$this->shouldCache()) {
            return;
        }

        Cache::put($this->getFullKey($cacheKey), $data, $this->cacheTtl);
    }

    /**
     * Check if cache key exists
     * 
     * @param string $cacheKey
     * @return bool
     */
    public function has(string $cacheKey): bool
    {
        if (!$this->shouldCache()) {
            return false;
        }

        return Cache::has($this->getFullKey($cacheKey));
    }

    /**
     * Forget cache key
     * 
     * @param string $cacheKey
     * @return void
     */
    public function forget(string $cacheKey): void
    {
        Cache::forget($this->getFullKey($cacheKey));
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
    public function generateKey(array $filters, int $limit, string $context, bool $single, ?string $className = null): string
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
    public function clear(string $context = 'all', ?string $className = null): void
    {
        // Note: This is a simplified cache clearing. In production, you might want to use Redis SCAN
        // or implement a more sophisticated cache clearing mechanism
        Cache::flush();
    }

    /**
     * Get full cache key with prefix
     * 
     * @param string $key
     * @return string
     */
    private function getFullKey(string $key): string
    {
        return $this->cachePrefix . ':' . $key;
    }

    /**
     * Set cache TTL
     * 
     * @param int $ttl
     * @return self
     */
    public function setTtl(int $ttl): self
    {
        $this->cacheTtl = $ttl;
        return $this;
    }

    /**
     * Enable/disable caching
     * 
     * @param bool $enable
     * @return self
     */
    public function setEnabled(bool $enable): self
    {
        $this->enableCaching = $enable;
        return $this;
    }

    /**
     * Set cache prefix
     * 
     * @param string $prefix
     * @return self
     */
    public function setPrefix(string $prefix): self
    {
        $this->cachePrefix = $prefix;
        return $this;
    }
} 