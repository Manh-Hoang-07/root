<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Traits\LoggingTrait;
use App\Libraries\CacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * Abstract Base Controller for API endpoints
 * 
 * Provides common CRUD operations with optimized data loading,
 * flexible relations handling, and standardized response formatting.
 * 
 * @package App\Http\Controllers\Api
 */
abstract class BaseController extends Controller
{
    use ResponseTrait;
    use LoggingTrait;

    /** @var mixed Service instance for business logic */
    protected $service;
    
    /** @var string Request class for store operations */
    protected $storeRequestClass = Request::class;
    
    /** @var string Request class for update operations */
    protected $updateRequestClass = Request::class;
    
    /** @var array Default relations to load for index operations */
    protected $indexRelations = [];
    
    /** @var array Default relations to load for show operations */
    protected $showRelations = [];
    
    /** @var int Default number of items per page */
    protected $defaultPerPage = 20;
    
    /** @var int Maximum number of items per page */
    protected $maxPerPage = 100;
    
    /** @var string Response format type */
    protected $responseFormat = 'json';
    
    /** @var bool Enable caching for responses */
    protected $enableCaching = false;
    
    /** @var int Cache TTL in seconds */
    protected $cacheTtl = 300; // 5 minutes
    
    /** @var bool Enable rate limiting */
    protected $enableRateLimiting = false;
    
    /** @var int Rate limit attempts per minute */
    protected $rateLimitAttempts = 60;
    
    /** @var int Default search limit */
    protected static $defaultSearchLimit = 10;
    
    /** @var CacheService Cache service instance */
    protected $cacheService;

    /**
     * Constructor
     * 
     * @param mixed $service Service instance
     */
    public function __construct($service)
    {
        $this->service = $service;
        
        // Initialize cache service
        $this->cacheService = new CacheService(
            $this->enableCaching ?? false,
            $this->cacheTtl ?? 300,
            'api'
        );
    }

    /**
     * Get the store request class
     * 
     * @return string
     */
    protected function getStoreRequestClass(): string
    {
        return $this->storeRequestClass;
    }

    /**
     * Get the update request class
     * 
     * @return string
     */
    protected function getUpdateRequestClass(): string
    {
        return $this->updateRequestClass;
    }

    /**
     * Display a listing of the resource
     * 
     * @param Request $request
     * @return JsonResponse|JsonResource
     */
    public function index(Request $request)
    {
        try {
            // Check rate limiting
            if ($this->enableRateLimiting && !$this->checkRateLimit($request)) {
                return $this->errorResponse('Quá nhiều yêu cầu. Vui lòng thử lại sau.', 429);
            }
            
            return $this->getIndexData($request);
        } catch (Exception $e) {
            $this->logError('Index', $e);
            
            return $this->errorResponse('Không thể tải danh sách dữ liệu');
        }
    }
    
    /**
     * Get index data with optimized loading
     * 
     * @param Request $request
     * @return JsonResponse|JsonResource
     */
    protected function getIndexData(Request $request)
    {
        $filters = $request->all();
        $perPage = min($request->get('per_page', $this->defaultPerPage), $this->maxPerPage);
        
        $data = $this->getOptimizedData($filters, $perPage, 'index');
        
        // Check if data is paginated (Laravel pagination object)
        if (method_exists($data, 'toArray')) {
            $dataArray = $data->toArray();
            // Check for Laravel pagination structure
            if (isset($dataArray['data']) && (isset($dataArray['links']) || isset($dataArray['meta']))) {
                // Format the data array within pagination
                $dataArray['data'] = $this->formatCollectionData($dataArray['data']);
                
                // If meta data is at top level, move it to meta key
                if (!isset($dataArray['meta']) && isset($dataArray['current_page'])) {
                    $metaKeys = ['current_page', 'from', 'last_page', 'last_page_url', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to', 'total'];
                    $meta = [];
                    foreach ($metaKeys as $key) {
                        if (isset($dataArray[$key])) {
                            $meta[$key] = $dataArray[$key];
                            unset($dataArray[$key]);
                        }
                    }
                    $dataArray['meta'] = $meta;
                }
                
                return response()->json($dataArray);
            }
        }
        
        return $this->successResponse(
            $this->formatResponse($data),
            'Lấy danh sách dữ liệu thành công'
        );
    }

    /**
     * Display the specified resource
     * 
     * @param int|string $id
     * @param Request|null $request
     * @return JsonResponse|JsonResource
     */
    public function show($id, ?Request $request = null)
    {
        try {
            return $this->getShowData($id, $request);
        } catch (Exception $e) {
            $this->logError('Show', $e, ['id' => $id]);
            
            return $this->errorResponse('Không thể tải thông tin chi tiết');
        }
    }
    
    /**
     * Get show data with optimized loading
     * 
     * @param int|string $id
     * @param Request|null $request
     * @return JsonResponse|JsonResource
     */
    protected function getShowData($id, ?Request $request = null)
    {
        $filters = $request ? $request->all() : [];
        $filters['id'] = $id;
        
        $item = $this->getOptimizedData($filters, 1, 'show', true);
        
        if (!$item) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }
        
        return $this->successResponse(
            $this->formatResponse($item, 'single'),
            'Lấy thông tin chi tiết thành công'
        );
    }

    /**
     * Get optimized data with common logic
     * 
     * @param array $filters
     * @param int $limit
     * @param string $context
     * @param bool $single
     * @return mixed
     */
    protected function getOptimizedData(array $filters, int $perPage, string $context = 'index', bool $single = false)
    {
        // Check caching
        if ($this->cacheService->shouldCache()) {
            $cacheKey = $this->cacheService->generateKey($filters, $perPage, $context, $single, static::class);
            $cachedData = $this->cacheService->get($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
        }

        // Parse relations from request
        $requestRelations = $this->parseRelations($filters['relations'] ?? null);
        
        // Use context-specific relations if no relations requested
        $defaultRelations = $context === 'show' ? $this->showRelations : $this->indexRelations;
        $relations = !empty($requestRelations) ? $requestRelations : $defaultRelations;
        
        // Parse fields from request
        $requestFields = $filters['fields'] ?? null;
        $fields = $this->parseFields($requestFields);
        
        // Optimize: Use default fields if none specified
        if (empty($fields) || $fields === ['*']) {
            $fields = $context === 'show' ? $this->getDefaultShowFields() : $this->getDefaultListFields();
        }
        
        // Remove non-filter parameters
        unset($filters['relations'], $filters['fields'], $filters['per_page']);
        
        if ($single) {
            $data = $this->service->find($filters['id'], $relations, $fields);
        } else {
            $data = $this->service->list($filters, $perPage, $relations, $fields);
        }

        // Cache the response if enabled
        if ($this->cacheService->shouldCache()) {
            $this->cacheService->put($cacheKey, $data);
        }

        return $data;
    }

    /**
     * Store a newly created resource
     * 
     * @return JsonResponse|JsonResource
     */
    public function store()
    {
        try {
            $request = app($this->getStoreRequestClass());
            $data = $this->service->create($request->validated());
            
            return $this->successResponse(
                $this->formatResponse($data, 'single'),
                'Tạo dữ liệu thành công'
            );
        } catch (Exception $e) {
            $this->logError('Store', $e);
            
            return $this->errorResponse('Không thể tạo dữ liệu');
        }
    }

    /**
     * Update the specified resource
     * 
     * @param int|string $id
     * @return JsonResponse|JsonResource
     */
    public function update($id)
    {
        try {
            $request = app($this->getUpdateRequestClass());
            $data = $this->service->update($id, $request->validated());
            
            if (!$data) {
                return $this->errorResponse('Không tìm thấy dữ liệu để cập nhật', 404);
            }
            
            return $this->successResponse(
                $this->formatResponse($data, 'single'),
                'Cập nhật dữ liệu thành công'
            );
        } catch (Exception $e) {
            $this->logError('Update', $e, ['id' => $id]);
            
            return $this->errorResponse('Không thể cập nhật dữ liệu');
        }
    }

    /**
     * Remove the specified resource
     * 
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $result = $this->service->delete($id);
            
            if ($result) {
                return $this->deletedResponse();
            }
            
            return $this->errorResponse('Không thể xóa dữ liệu');
        } catch (Exception $e) {
            $this->logError('Destroy', $e, ['id' => $id]);
            
            return $this->errorResponse('Không thể xóa dữ liệu');
        }
    }

    /**
     * Parse relations from request
     * 
     * @param mixed $relations
     * @return array
     */
    protected function parseRelations($relations): array
    {
        if (is_array($relations)) {
            return $relations;
        }
        
        if (is_string($relations)) {
            return array_filter(array_map('trim', explode(',', $relations)));
        }
        
        return [];
    }

    /**
     * Parse fields from request
     * 
     * @param mixed $fields
     * @return array
     */
    protected function parseFields($fields): array
    {
        if (is_array($fields)) {
            return $fields;
        }
        
        if (is_string($fields)) {
            return array_filter(array_map('trim', explode(',', $fields)));
        }
        
        return ['*'];
    }

    /**
     * Parse and clean request data
     * 
     * @param Request $request
     * @return array
     */
    protected function parseRequestData(Request $request): array
    {
        $data = $request->all();
        
        // Remove empty values
        $data = array_filter($data, function($value) {
            return $value !== '' && $value !== null;
        });
        
        return $data;
    }

    /**
     * Format response based on type
     * 
     * @param mixed $data
     * @param string $type
     * @return array
     */
    protected function formatResponse($data, string $type = 'collection')
    {
        if ($type === 'single') {
            return $this->formatSingleData($data);
        }
        
        // Check if data is a paginated collection (Laravel pagination object)
        if (method_exists($data, 'toArray')) {
            $dataArray = $data->toArray();
            // Check for Laravel pagination structure
            if (isset($dataArray['data']) && (isset($dataArray['links']) || isset($dataArray['meta']))) {
                // Format the data array within pagination
                $dataArray['data'] = $this->formatCollectionData($dataArray['data']);
                
                // If meta data is at top level, move it to meta key
                if (!isset($dataArray['meta']) && isset($dataArray['current_page'])) {
                    $metaKeys = ['current_page', 'from', 'last_page', 'last_page_url', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to', 'total'];
                    $meta = [];
                    foreach ($metaKeys as $key) {
                        if (isset($dataArray[$key])) {
                            $meta[$key] = $dataArray[$key];
                            unset($dataArray[$key]);
                        }
                    }
                    $dataArray['meta'] = $meta;
                }
                
                return $dataArray;
            }
        }
        
        return $this->formatCollectionData($data);
    }

    /**
     * Format single data without Resource
     * 
     * @param mixed $data
     * @return array
     */
    protected function formatSingleData($data)
    {
        if (!$data) {
            return null;
        }

        // If data is already an array, return as is
        if (is_array($data)) {
            return $data;
        }

        // Convert model to array with proper formatting
        $formatted = $data->toArray();

        // Format timestamps consistently
        $formatted = $this->formatTimestamps($formatted);

        // Format relationships if they exist
        $formatted = $this->formatRelationships($formatted, $data);

        return $formatted;
    }

    /**
     * Format collection data without Resource
     * 
     * @param mixed $data
     * @return array
     */
    protected function formatCollectionData($data)
    {
        if (!$data) {
            return [];
        }

        // If data is already an array, return as is
        if (is_array($data)) {
            return $data;
        }

        // Convert collection to array
        if (method_exists($data, 'map')) {
            $formatted = $data->map(function ($item) {
                return $this->formatSingleData($item);
            })->toArray();
        } else {
            $formatted = $data;
        }

        return $formatted;
    }

    /**
     * Format timestamps in data
     * 
     * @param array $data
     * @return array
     */
    protected function formatTimestamps(array $data): array
    {
        $timestampFields = ['created_at', 'updated_at', 'deleted_at', 'email_verified_at', 'phone_verified_at', 'last_login_at'];
        
        foreach ($timestampFields as $field) {
            if (isset($data[$field]) && $data[$field]) {
                if (is_string($data[$field])) {
                    $data[$field] = date('Y-m-d H:i:s', strtotime($data[$field]));
                } elseif (is_object($data[$field]) && method_exists($data[$field], 'toDateTimeString')) {
                    $data[$field] = $data[$field]->toDateTimeString();
                }
            }
        }

        return $data;
    }

    /**
     * Format relationships in data
     * 
     * @param array $data
     * @param mixed $originalData
     * @return array
     */
    protected function formatRelationships(array $data, $originalData): array
    {
        // Handle common relationships
        $relationshipFields = ['profile', 'roles', 'brand', 'categories', 'variants', 'images'];
        
        foreach ($relationshipFields as $field) {
            if (isset($data[$field]) && is_object($data[$field])) {
                if (method_exists($data[$field], 'toArray')) {
                    $data[$field] = $data[$field]->toArray();
                }
            } elseif (isset($data[$field]) && is_array($data[$field])) {
                // Handle collection relationships
                $data[$field] = array_map(function ($item) {
                    if (is_object($item) && method_exists($item, 'toArray')) {
                        return $item->toArray();
                    }
                    return $item;
                }, $data[$field]);
            }
        }

        return $data;
    }

    /**
     * Search resources with flexible configuration
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $filters = $this->parseRequestData($request);
            $limit = min($request->get('limit', static::$defaultSearchLimit), $this->maxPerPage);
            
            // Get search-specific configuration
            $fields = $this->getSearchFields();
            $relations = $this->getSearchRelations();
            
            $results = $this->service->list($filters, $limit, $relations, $fields);
            
            return $this->successResponse(
                $this->formatResponse($results),
                'Tìm kiếm dữ liệu thành công'
            );
        } catch (Exception $e) {
            $this->logError('Search', $e);
            
            return $this->errorResponse('Không thể tìm kiếm dữ liệu');
        }
    }

    /**
     * Get default fields for list view
     * 
     * @return array
     */
    protected function getDefaultListFields(): array
    {
        return ['*'];
    }

    /**
     * Get default fields for show view
     * 
     * @return array
     */
    protected function getDefaultShowFields(): array
    {
        return ['*'];
    }

    /**
     * Get fields for search operation
     * 
     * @return array
     */
    protected function getSearchFields(): array
    {
        return ['id', 'name'];
    }

    /**
     * Get relations for search operation
     * 
     * @return array
     */
    protected function getSearchRelations(): array
    {
        return [];
    }



    /**
     * Check rate limiting
     * 
     * @param Request $request
     * @return bool
     */
    protected function checkRateLimit(Request $request): bool
    {
        $key = $this->generateRateLimiterKey($request);
        
        // Simple rate limiting implementation
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $this->rateLimitAttempts) {
            return false;
        }
        
        Cache::put($key, $attempts + 1, 60); // 1 minute window
        return true;
    }

    /**
     * Generate a unique key for rate limiter
     * 
     * @param Request $request
     * @return string
     */
    protected function generateRateLimiterKey(Request $request): string
    {
        return 'rate_limit:' . $request->ip() . ':' . $request->path();
    }
} 