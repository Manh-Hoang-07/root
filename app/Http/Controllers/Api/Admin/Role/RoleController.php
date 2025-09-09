<?php
namespace App\Http\Controllers\Api\Admin\Role;

use App\Http\Controllers\Api\BaseController;
use App\Services\Role\RoleService;
use App\Http\Requests\Admin\Role\RoleRequest;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    public function __construct(RoleService $service)
    {
        parent::__construct($service);
                $this->storeRequestClass = RoleRequest::class;
        $this->updateRequestClass = RoleRequest::class;
    }

    /**
     * Override index method để wrap response
     */
    public function index(Request $request)
    {
        try {
            $data = $this->getIndexData($request);
            return $this->successResponse($data, 'Lấy danh sách vai trò thành công');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
} 
