<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

abstract class BaseController extends Controller
{
    protected $service;
    protected $resource;
    protected $listResource; // Thêm listResource
    protected $storeRequestClass = Request::class;
    protected $updateRequestClass = Request::class;
    
    // Tự động load relationships cho tất cả requests
    protected $defaultRelations = [];
    
    // Tự động load relationships cho index method
    protected $indexRelations = [];
    
    // Tự động load relationships cho show method
    protected $showRelations = [];
    
    // Cache settings
    protected $enableCache = true;
    protected $cacheTtl = 300; // 5 minutes
    protected $cachePrefix = 'api_';

    public function __construct($service, $resource)
    {
        $this->service = $service;
        $this->resource = $resource;
        
        // Tự động tạo listResource nếu không được set
        if (!$this->listResource) {
            $resourceClass = $resource;
            $listResourceClass = str_replace('Resource', 'ListResource', $resourceClass);
            
            // Kiểm tra xem ListResource có tồn tại không
            if (class_exists($listResourceClass)) {
                $this->listResource = $listResourceClass;
            } else {
                $this->listResource = $resource; // Fallback về resource gốc
            }
        }
    }

    protected function getStoreRequestClass()
    {
        return $this->storeRequestClass;
    }

    protected function getUpdateRequestClass()
    {
        return $this->updateRequestClass;
    }

    public function index(Request $request)
    {
        if (!$this->enableCache) {
            return $this->getIndexData($request);
        }
        
        $cacheKey = $this->generateCacheKey('index', $request);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($request) {
            return $this->getIndexData($request);
        });
    }
    
    protected function getIndexData(Request $request)
    {
        // Parse relations từ request
        $requestRelations = $this->parseRelations($request->get('relations'));
        
        // Chỉ sử dụng relations được yêu cầu, không có default
        $relations = $requestRelations;
        
        $fields = $this->parseFields($request->get('fields'));
        $data = $this->service->list($request->all(), $request->get('per_page', 20), $relations, $fields);
        
        // Sử dụng listResource cho index
        return $this->listResource::collection($data);
    }

    public function show($id, Request $request = null)
    {
        if (!$this->enableCache) {
            return $this->getShowData($id, $request);
        }
        
        $cacheKey = $this->generateCacheKey('show', $request, $id);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $request) {
            return $this->getShowData($id, $request);
        });
    }
    
    protected function getShowData($id, Request $request = null)
    {
        // Parse relations từ request
        $requestRelations = $request ? $this->parseRelations($request->get('relations')) : [];
        
        // Chỉ sử dụng relations được yêu cầu, không có default
        $relations = $requestRelations;
        
        $fields = $request ? $this->parseFields($request->get('fields')) : ['*'];
        $item = $this->service->find($id, $relations, $fields);
        return new $this->resource($item);
    }

    public function store()
    {
        $request = app($this->getStoreRequestClass());
        $data = $this->parseRequestData($request);
        $item = $this->service->create($data);
        
        // Load relations cho response nếu cần
        if (!empty($this->defaultRelations)) {
            $item->load($this->defaultRelations);
        }
        
        // Invalidate cache
        $this->invalidateCache();
        
        return new $this->resource($item);
    }

    public function update($id)
    {
        $request = app($this->getUpdateRequestClass());
        
        $data = $this->parseRequestData($request);
        
        try {
            $item = $this->service->update($id, $data);
            
            // Load relations cho response nếu cần
            if (!empty($this->defaultRelations)) {
                $item->load($this->defaultRelations);
            }
            
            // Invalidate cache
            $this->invalidateCache();
            
            return new $this->resource($item);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            
            // Invalidate cache
            $this->invalidateCache();
            
            return response()->json(['success' => true]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Generate cache key for requests
     */
    protected function generateCacheKey($method, Request $request = null, $id = null)
    {
        $params = $request ? $request->all() : [];
        ksort($params);
        
        $key = $this->cachePrefix . static::class . '_' . $method;
        if ($id) {
            $key .= '_' . $id;
        }
        $key .= '_' . md5(serialize($params));
        
        return $key;
    }
    
    /**
     * Invalidate all cache for this controller
     */
    protected function invalidateCache()
    {
        if (!$this->enableCache) {
            return;
        }
        
        $pattern = $this->cachePrefix . static::class . '_*';
        
        // Get all cache keys matching the pattern
        $keys = Cache::get($pattern) ?: [];
        
        // Clear specific cache keys
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        // Also clear by pattern (if using Redis)
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys($pattern);
            foreach ($keys as $key) {
                $redis->del($key);
            }
        }
        
        // Log cache invalidation
        Log::info('Cache invalidated', [
            'controller' => static::class,
            'pattern' => $pattern
        ]);
    }
    
    /**
     * Get cached data with fallback
     */
    protected function getCachedData($key, $callback, $ttl = null)
    {
        if (!$this->enableCache) {
            return $callback();
        }
        
        $ttl = $ttl ?: $this->cacheTtl;
        return Cache::remember($key, $ttl, $callback);
    }

    protected function parseRelations($relations)
    {
        if (is_array($relations)) return $relations;
        if (is_string($relations)) {
            return array_filter(array_map('trim', explode(',', $relations)));
        }
        return [];
    }

    protected function parseFields($fields)
    {
        if (is_array($fields)) return $fields;
        if (is_string($fields)) {
            return array_filter(array_map('trim', explode(',', $fields)));
        }
        return ['*'];
    }

    /**
     * Parse request data to handle both FormData and JSON
     */
    protected function parseRequestData(Request $request)
    {
        if ($request instanceof \Illuminate\Foundation\Http\FormRequest) {
            $data = $request->validated();
        } else {
            $data = $request->all();
        }

        if (empty($data) && $request->header('Content-Type') && str_contains($request->header('Content-Type'), 'multipart/form-data')) {
            $data = $request->input();
        }
        
        // Chỉ loại bỏ empty string và empty array, giữ lại null
        return array_filter($data, function($value) {
            return $value !== '' && $value !== [];
        });
    }

    /**
     * Trả về response thành công
     */
    protected function successResponse($data = null, string $message = 'Thành công', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Trả về response lỗi
     */
    protected function errorResponse(string $message = 'Có lỗi xảy ra', int $statusCode = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], $statusCode);
    }

    /**
     * Trả về response không tìm thấy
     */
    protected function notFoundResponse(string $message = 'Không tìm thấy dữ liệu'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Trả về response validation error
     */
    protected function validationErrorResponse(array $errors, string $message = 'Dữ liệu không hợp lệ'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'data' => null
        ], 422);
    }

    /**
     * Trả về response unauthorized
     */
    protected function unauthorizedResponse(string $message = 'Không có quyền truy cập'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Trả về response forbidden
     */
    protected function forbiddenResponse(string $message = 'Truy cập bị từ chối'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }
} 
