<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

abstract class BaseController extends Controller
{
    protected $service;
    protected $resource;
    protected $listResource;
    protected $storeRequestClass = Request::class;
    protected $updateRequestClass = Request::class;
    
    // Context-specific relations
    protected $indexRelations = [];
    protected $showRelations = [];
    
    // Pagination defaults
    protected $defaultPerPage = 20;
    protected $maxPerPage = 100;
    
    // Response format
    protected $responseFormat = 'json'; // json, resource, collection

    public function __construct($service, $resource)
    {
        $this->service = $service;
        $this->resource = $resource;
        
        // Auto-generate listResource if not set
        if (!$this->listResource) {
            $resourceClass = $resource;
            $listResourceClass = str_replace('Resource', 'ListResource', $resourceClass);
            
            if (class_exists($listResourceClass)) {
                $this->listResource = $listResourceClass;
            } else {
                $this->listResource = $resource;
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
        return $this->getIndexData($request);
    }
    
    protected function getIndexData(Request $request)
    {
        // Parse relations từ request
        $requestRelations = $this->parseRelations($request->get('relations'));
        
        // Sử dụng indexRelations nếu không có relations được yêu cầu
        $relations = !empty($requestRelations) ? $requestRelations : $this->indexRelations;
        
        $fields = $this->parseFields($request->get('fields'));
        $perPage = min($request->get('per_page', $this->defaultPerPage), $this->maxPerPage);
        
        $data = $this->service->list($request->all(), $perPage, $relations, $fields);
        
        return $this->formatResponse($data);
    }

    public function show($id, Request $request = null)
    {
        return $this->getShowData($id, $request);
    }
    
    protected function getShowData($id, Request $request = null)
    {
        // Parse relations từ request
        $requestRelations = $request ? $this->parseRelations($request->get('relations')) : [];
        
        // Sử dụng showRelations nếu không có relations được yêu cầu
        $relations = !empty($requestRelations) ? $requestRelations : $this->showRelations;
        
        $fields = $request ? $this->parseFields($request->get('fields')) : ['*'];
        $item = $this->service->find($id, $relations, $fields);
        
        return $this->formatResponse($item, 'single');
    }

    public function store()
    {
        $request = app($this->getStoreRequestClass());
        $data = $this->parseRequestData($request);
        $item = $this->service->create($data);
        
        return $this->formatResponse($item, 'single');
    }

    public function update($id)
    {
        $request = app($this->getUpdateRequestClass());
        $data = $this->parseRequestData($request);
        
        try {
            $item = $this->service->update($id, $data);
            return $this->formatResponse($item, 'single');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return $this->successResponse(null, 'Xóa thành công');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
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
        
        return array_filter($data, function($value) {
            return $value !== '' && $value !== [];
        });
    }

    /**
     * Format response based on type
     */
    protected function formatResponse($data, $type = 'collection')
    {
        if ($this->responseFormat === 'json') {
            return response()->json($data);
        }
        
        if ($type === 'single') {
            return new $this->resource($data);
        }
        
        return $this->listResource::collection($data);
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

    /**
     * Simple search method for all controllers
     */
    public function search(Request $request)
    {
        $filters = [];
        
        // Xử lý search text
        if ($request->get('search')) {
            $filters['search'] = $request->get('search');
        }
        
        // Xử lý ID
        if ($request->get('id')) {
            $filters['id'] = $request->get('id');
        }
        
        // Xử lý IDs
        if ($request->get('ids')) {
            $filters['ids'] = $request->get('ids');
        }
        
        // Xử lý các filters khác
        if ($request->get('filters')) {
            $filters = array_merge($filters, $request->get('filters'));
        }
        
        $limit = min($request->get('limit', 10), 100);
        $fields = $request->get('fields', ['id', 'name']);
        
        // Sử dụng service để lấy data
        $results = $this->service->list($filters, $limit, [], $fields);
        
        // Format kết quả cho select dropdown
        $data = $results->map(function ($item) {
            $valueField = request('value_field', 'id');
            $labelField = request('label_field', 'name');
            
            return [
                'value' => $item->{$valueField},
                'label' => $item->{$labelField}
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
} 