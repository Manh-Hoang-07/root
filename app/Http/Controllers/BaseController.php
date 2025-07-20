<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

abstract class BaseController extends Controller
{
    protected $service;
    protected $resource;

    public function __construct($service, $resource)
    {
        $this->service = $service;
        $this->resource = $resource;
    }

    public function index(Request $request)
    {
        $relations = $this->parseRelations($request->get('relations'));
        $fields = $this->parseFields($request->get('fields'));
        $data = $this->service->list($request->all(), $request->get('per_page', 20), $relations, $fields);
        return $this->resource::collection($data);
    }

    public function show($id, Request $request = null)
    {
        $relations = $request ? $this->parseRelations($request->get('relations')) : [];
        $fields = $request ? $this->parseFields($request->get('fields')) : ['*'];
        $item = $this->service->find($id, $relations, $fields);
        return new $this->resource($item);
    }

    public function store(Request $request)
    {
        $data = $this->parseRequestData($request);
        $item = $this->service->create($data);
        return new $this->resource($item);
    }

    public function update(Request $request, $id)
    {
        Log::info('BaseController::update', [
            'id' => $id,
            'request_data' => $request->all(),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'form_data' => $request->input(),
            'files' => $request->allFiles()
        ]);
        
        $data = $this->parseRequestData($request);
        
        $item = $this->service->update($id, $data);
        
        Log::info('BaseController::update result', [
            'updated_item' => $item->toArray()
        ]);
        
        return new $this->resource($item);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['success' => true]);
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
        $data = $request->all();
        
        // Nếu data rỗng và là FormData, thử parse lại
        if (empty($data) && $request->header('Content-Type') && str_contains($request->header('Content-Type'), 'multipart/form-data')) {
            $data = $request->input();
            Log::info('BaseController::parseRequestData - parsed FormData', ['data' => $data]);
        }
        
        // Loại bỏ các trường rỗng
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '' && $value !== [];
        });
        
        return $data;
    }
} 