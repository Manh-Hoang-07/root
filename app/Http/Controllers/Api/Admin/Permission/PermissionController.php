<?php
namespace App\Http\Controllers\Api\Admin\Permission;

use App\Http\Controllers\BaseController;
use App\Services\Permission\PermissionService;
use App\Http\Resources\Admin\Permission\PermissionResource;
use App\Http\Requests\Admin\Permission\PermissionRequest;
use Illuminate\Http\Request;

class PermissionController extends BaseController
{
    public function __construct(PermissionService $service)
    {
        parent::__construct($service, PermissionResource::class);
        $this->storeRequestClass = PermissionRequest::class;
        $this->updateRequestClass = PermissionRequest::class;
    }

    public function index(Request $request)
    {
        $relations = ['parent']; // Chỉ load parent, không load children
        $fields = $this->parseFields($request->get('fields'));
        $data = $this->service->list($request->all(), $request->get('per_page', 20), $relations, $fields);
        return $this->resource::collection($data);
    }

    public function show($id, Request $request = null)
    {
        $relations = ['parent', 'children']; // Chỉ load children khi cần thiết (show detail)
        $fields = $request ? $this->parseFields($request->get('fields')) : ['*'];
        $item = $this->service->find($id, $relations, $fields);
        return new $this->resource($item);
    }

    public function update($id)
    {
        try {
            $request = app($this->getUpdateRequestClass());
            $data = $this->parseRequestData($request);
            $item = $this->service->update($id, $data);
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
} 