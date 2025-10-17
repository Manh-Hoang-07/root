<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Controller;
use App\Libraries\Core\CacheService;
use App\Services\BaseService;
use App\Traits\LoggingTrait;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * Abstract Base Controller for API endpoints
 * Provides common CRUD operations with optimized data loading,
 * flexible relations handling, and standardized response formatting.
 *
 * @package App\Http\Controllers\Api
 */
abstract class ListController extends Controller
{
    use ResponseTrait;
    use LoggingTrait;

    /**
     * Service instance for business logic
     * @var BaseService
     */
    protected $service;

    /** @var string Request class for store operations */
    protected $storeRequestClass = Request::class;

    /** @var string Request class for update operations */
    protected $updateRequestClass = Request::class;

    /** @var string Request class for status update operations */
    protected $statusUpdateRequestClass = Request::class;

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
     * @param BaseService $service Service instance
     */
    public function __construct(BaseService $service)
    {
        $this->service = $service;
    }

    /**
     * Get the store request class
     * @return string
     */
    protected function getStoreRequestClass(): string
    {
        return $this->storeRequestClass;
    }

    /**
     * Get the update request class
     * @return string
     */
    protected function getUpdateRequestClass(): string
    {
        return $this->updateRequestClass;
    }

    /**
     * Get the status update request class
     * @return string
     */
    protected function getStatusUpdateRequestClass(): string
    {
        return $this->statusUpdateRequestClass;
    }

    /**
     * Display a listing of the resource
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check rate limiting with advanced implementation
            if ($this->enableRateLimiting && !$this->checkAdvancedRateLimit($request)) {
                return $this->apiResponse(false, null, 'Quá nhiều yêu cầu, vui lòng thử lại sau', 429);
            }
            return $this->getIndexData($request);
        } catch (Exception $e) {
            $this->logError('Index', $e);
            return $this->apiResponse(false, null, 'Không thể tải danh sách dữ liệu', 500);
        }
    }

    /**
     * Get index data with optimized loading
     * @param Request $request
     * @return JsonResponse
     */
    protected function getIndexData(Request $request): JsonResponse
    {
        $filters = $request->all();
        $perPage = $this->getValidatedPerPage($request);
        $data = $this->getOptimizedData($filters, $perPage, 'index');
        return $this->successResponseWithFormat($data, 'Lấy danh sách dữ liệu thành công');
    }

    /**
     * Get validated per page value
     * @param Request $request
     * @return int
     */
    private function getValidatedPerPage(Request $request): int
    {
        return min($request->get('per_page', $this->defaultPerPage), $this->maxPerPage);
    }

    /**
     * Display the specified resource
     * @param int|string $id
     * @param Request|null $request
     * @return JsonResponse
     */
    public function show($id, ?Request $request = null): JsonResponse
    {
        try {
            return $this->getShowData($id, $request);
        } catch (Exception $e) {
            $this->logError('Show', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể tải thông tin chi tiết', 500);
        }
    }

    /**
     * Get show data with optimized loading
     * @param int|string $id
     * @param Request|null $request
     * @return JsonResponse
     */
    protected function getShowData($id, ?Request $request = null): JsonResponse
    {
        $filters = $request ? $request->all() : [];
        $filters['id'] = $id;
        $item = $this->getOptimizedData($filters, 1, 'show', true);
        if (!$item) {
            return $this->apiResponse(false, null, '', 404);
        }
        return $this->successResponseWithFormat($item, 'Lấy thông tin chi tiết thành công', 200);
    }

    /**
     * Process filters before querying - can be overridden in child classes
     * @param array $filters
     * @param string $context
     * @return array
     */
    protected function processFilters(array $filters, string $context = 'index'): array
    {
        return $filters;
    }

    /**
     * Get optimized data with common logic
     * @param array $filters
     * @param int $limit
     * @param string $context
     * @param bool $single
     * @return array
     */
    protected function getOptimizedData(array $filters, int $perPage, string $context = 'index', bool $single = false): array
    {
        // Process filters - allow child classes to modify filters
        $filters = $this->processFilters($filters, $context);

        // Check caching
        if ($this->enableCaching) {
            $cacheKey = CacheService::generateKey($filters, $perPage, $context, $single, static::class);
            $cachedData = CacheService::get($cacheKey, 'api');
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
            $data = $data ?? []; // Ensure array return
        } else {
            $data = $this->service->list($filters, $perPage, $relations, $fields);
        }
        // Cache the response if enabled
        if ($this->enableCaching) {
            CacheService::put($cacheKey, $data, $this->cacheTtl, 'api');
        }
        return $data;
    }

    /**
     * Store a newly created resource
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        try {
            $request = app($this->getStoreRequestClass());
            $data = $this->service->create($request->validated());
            return $this->successResponseWithFormat($data, 'Tạo dữ liệu thành công', 201);
        } catch (ValidationException|HttpResponseException $e) {
            throw $e; // Let the framework return 422 with validation errors
        } catch (Exception $e) {
            $this->logError('Store', $e);
            return $this->apiResponse(false, null, 'Không thể tạo dữ liệu', 500);
        }
    }

    /**
     * Update the specified resource
     * @param int|string $id
     * @return JsonResponse
     */
    public function update($id): JsonResponse
    {
        try {
            $request = app($this->getUpdateRequestClass());
            $data = $this->service->update($id, $request->validated());
            if (!$data) {
                return $this->apiResponse(false, null, '', 404);
            }
            return $this->successResponseWithFormat($data, 'Cập nhật dữ liệu thành công', 200);
        } catch (ValidationException|HttpResponseException $e) {
            throw $e; // Let the framework return 422 with validation errors
        } catch (Exception $e) {
            $this->logError('Update', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể cập nhật dữ liệu', 500);
        }
    }

    /**
     * Remove the specified resource
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $result = $this->service->delete($id);
            if ($result) {
                return $this->apiResponse(true, null, '', 200);
            }
            return $this->apiResponse(false, null, 'Không thể xóa dữ liệu', 500);
        } catch (Exception $e) {
            $this->logError('Destroy', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể xóa dữ liệu', 500);
        }
    }

    /**
     * Parse relations from request
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
     * Search resources with flexible configuration
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $filters = $this->parseRequestData($request);
            $limit = min($request->get('limit', static::$defaultSearchLimit), $this->maxPerPage);
            // Get search-specific configuration
            $fields = $this->getSearchFields();
            $relations = $this->getSearchRelations();
            $results = $this->service->list($filters, $limit, $relations, $fields);
            return $this->successResponseWithFormat($results, 'Tìm kiếm dữ liệu thành công');
        } catch (Exception $e) {
            $this->logError('Search', $e);
            return $this->apiResponse(false, null, 'Không thể tìm kiếm dữ liệu', 500);
        }
    }

    /**
     * Get default fields for list view
     * @return array
     */
    protected function getDefaultListFields(): array
    {
        return ['*'];
    }

    /**
     * Get default fields for show view
     * @return array
     */
    protected function getDefaultShowFields(): array
    {
        return ['*'];
    }

    /**
     * Get fields for search operation
     * @return array
     */
    protected function getSearchFields(): array
    {
        return ['id', 'name'];
    }

    /**
     * Get relations for search operation
     * @return array
     */
    protected function getSearchRelations(): array
    {
        return [];
    }

    /**
     * Check rate limiting with atomic operations
     * @param Request $request
     * @return bool
     */
    protected function checkRateLimit(Request $request): bool
    {
        $key = $this->generateRateLimiterKey($request);

        // Use atomic increment to avoid race conditions
        $attempts = Cache::increment($key, 1);

        // If this is the first request in the window, set expiration
        if ($attempts === 1) {
            CacheService::put($key, 1, 60, 'rate_limit'); // 1 minute window
        }

        // Check if limit exceeded
        if ($attempts > $this->rateLimitAttempts) {
            return false;
        }

        return true;
    }

    /**
     * Generate a unique key for rate limiter
     * @param Request $request
     * @return string
     */
    protected function generateRateLimiterKey(Request $request): string
    {
        return 'rate_limit:' . $request->ip() . ':' . $request->path();
    }

    /**
     * Advanced rate limiting with sliding window (Redis recommended)
     * @param Request $request
     * @return bool
     */
    protected function checkAdvancedRateLimit(Request $request): bool
    {
        $key = $this->generateRateLimiterKey($request);
        $now = time();
        $window = 60; // 1 minute window

        // Use Redis sorted set for sliding window (if Redis is available)
        if (config('cache.default') === 'redis') {
            return $this->checkSlidingWindowRateLimit($key, $now, $window);
        }

        // Fallback to simple counter for non-Redis cache
        return $this->checkRateLimit($request);
    }

    /**
     * Sliding window rate limiting using Redis sorted sets
     * @param string $key
     * @param int $now
     * @param int $window
     * @return bool
     */
    protected function checkSlidingWindowRateLimit(string $key, int $now, int $window): bool
    {
        $redis = Cache::getRedis();
        $pipe = $redis->pipeline();

        // Remove expired entries
        $pipe->zremrangebyscore($key, 0, $now - $window);

        // Count current requests
        $pipe->zcard($key);

        // Add current request
        $pipe->zadd($key, $now, $now . ':' . uniqid());

        // Set expiration
        $pipe->expire($key, $window);

        $results = $pipe->exec();
        $currentCount = $results[1];

        return $currentCount < $this->rateLimitAttempts;
    }

    /**
     * Update status of a resource (only if different)
     * @param int|string $id
     * @return JsonResponse
     */
    public function updateStatus($id): JsonResponse
    {
        try {
            $request = app($this->getStatusUpdateRequestClass());
            $result = $this->service->updateStatus($id, $request->status);

            if (!$result) {
                return $this->apiResponse(false, null, 'Không tìm thấy dữ liệu để cập nhật', 404);
            }

            return $this->apiResponse(true, $result, 'Cập nhật trạng thái thành công');
        } catch (ValidationException|HttpResponseException $e) {
            throw $e; // Let the framework return 422 with validation errors
        } catch (Exception $e) {
            $this->logError('UpdateStatus', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể cập nhật trạng thái', 500);
        }
    }

}
