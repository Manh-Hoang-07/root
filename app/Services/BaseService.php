<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

abstract class BaseService
{
    protected $repo;
    protected $enableCache = true;
    protected $cacheTtl = 300; // 5 minutes

    public function __construct($repo)
    {
        $this->repo = $repo;
    }

    public function list($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
    {
        if (!$this->enableCache) {
            return $this->repo->all($filters, $perPage, $relations, $fields);
        }
        
        $cacheKey = $this->generateCacheKey('list', $filters, $perPage, $relations, $fields);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($filters, $perPage, $relations, $fields) {
            return $this->repo->all($filters, $perPage, $relations, $fields);
        });
    }

    public function find($id, $relations = [], $fields = ['*'])
    {
        if (!$this->enableCache) {
            return $this->repo->find($id, $relations, $fields);
        }
        
        $cacheKey = $this->generateCacheKey('find', $id, $relations, $fields);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $relations, $fields) {
            return $this->repo->find($id, $relations, $fields);
        });
    }

    public function create($data)
    {
        $data = $this->handleImageUpload($data);
        $result = $this->repo->create($data);
        
        // Clear cache after creation
        $this->clearCache();
        
        return $result;
    }

    public function update($id, $data)
    {
        $data = $this->handleImageUpload($data);
        $result = $this->repo->update($id, $data);
        
        // Clear cache after update
        $this->clearCache();
        
        return $result;
    }

    public function delete($id)
    {
        $item = $this->find($id);
        $result = $this->repo->delete($id);
        
        // Clear cache after deletion
        $this->clearCache();
        
        return $result;
    }

    public function getRepo()
    {
        return $this->repo;
    }
    
    /**
     * Generate cache key
     */
    protected function generateCacheKey($method, ...$params)
    {
        $key = strtolower(class_basename($this)) . '_' . $method;
        $key .= '_' . md5(serialize($params));
        return $key;
    }
    
    /**
     * Clear cache for this service
     */
    protected function clearCache()
    {
        if (!$this->enableCache) {
            return;
        }
        
        $pattern = strtolower(class_basename($this)) . '_*';
        
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
                // Log warning but don't throw exception
            }
        }
    }

    protected function handleImageUpload($data)
    {
        // Xử lý tự động cho trường image nếu là file upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('categories', 'public');
        }
        return $data;
    }
} 