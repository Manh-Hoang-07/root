<?php
namespace App\Http\Controllers\Api\Admin\Role;

use App\Http\Controllers\BaseController;
use App\Services\Role\RoleService;
use App\Http\Resources\Admin\Role\RoleResource;
use App\Http\Requests\Admin\Role\RoleRequest;

class RoleController extends BaseController
{
    public function __construct(RoleService $service)
    {
        parent::__construct($service, RoleResource::class);
        $this->storeRequestClass = RoleRequest::class;
        $this->updateRequestClass = RoleRequest::class;
    }
} 