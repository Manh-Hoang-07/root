<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $item = $this->service->create($request->all());
        return new $this->resource($item);
    }

    public function update(Request $request, $id)
    {
        $item = $this->service->update($id, $request->all());
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
} 