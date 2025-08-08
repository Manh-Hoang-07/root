<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

abstract class BaseController extends Controller
{
    use ResponseTrait;

    protected $service;
    protected $resource;
    protected $listResource;
    protected $storeRequestClass = Request::class;
    protected $updateRequestClass = Request::class;
    
    // Context-specific relations - tối ưu mặc định
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
        
        // Tối ưu: Chỉ load fields cần thiết cho list view
        if (empty($fields) || $fields === ['*']) {
            $fields = $this->getDefaultListFields();
        }
        
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
        $data = $this->service->create($request->validated());
        
        return $this->formatResponse($data, 'single');
    }

    public function update($id)
    {
        $request = app($this->getUpdateRequestClass());
        $data = $this->service->update($id, $request->validated());
        
        return $this->formatResponse($data, 'single');
    }

    public function destroy($id)
    {
        $result = $this->service->delete($id);
        
        if ($result) {
            return $this->deletedResponse();
        }
        
        return $this->errorResponse('Không thể xóa dữ liệu');
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

    protected function parseRequestData(Request $request)
    {
        $data = $request->all();
        
        // Remove empty values
        $data = array_filter($data, function($value) {
            return $value !== '' && $value !== null;
        });
        
        return $data;
    }

    protected function formatResponse($data, $type = 'collection')
    {
        if ($type === 'single') {
            return new $this->resource($data);
        }
        
        return $this->listResource::collection($data);
    }

    public function search(Request $request)
    {
        $filters = $this->parseRequestData($request);
        $limit = min($request->get('limit', 10), 100);
        
        // Tối ưu: Chỉ load fields cần thiết cho search
        $fields = ['id', 'name'];
        $relations = [];
        
        $results = $this->service->list($filters, $limit, $relations, $fields);
        
        return $this->successResponse($results);
    }

    /**
     * Tối ưu: Lấy fields mặc định cho list view
     */
    protected function getDefaultListFields()
    {
        return ['id', 'name', 'status', 'created_at'];
    }
} 