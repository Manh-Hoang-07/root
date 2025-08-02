<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

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
            return response()->json(['success' => true]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
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
