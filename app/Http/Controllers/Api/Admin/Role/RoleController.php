<?php
namespace App\Http\Controllers\Api\Admin\Role;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Role\RoleService;
use App\Http\Resources\Admin\Role\RoleResource;
use App\Http\Resources\Admin\Role\RoleListResource;
use App\Http\Requests\Admin\Role\RoleRequest;

class RoleController extends BaseController
{
    public function __construct(RoleService $service)
    {
        parent::__construct($service, RoleResource::class);
        $this->listResource = RoleListResource::class;
        $this->storeRequestClass = RoleRequest::class;
        $this->updateRequestClass = RoleRequest::class;
    }
} 
