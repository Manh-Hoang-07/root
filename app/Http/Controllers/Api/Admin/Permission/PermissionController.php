<?php
namespace App\Http\Controllers\Api\Admin\Permission;

use App\Http\Controllers\Api\BaseController;
use App\Services\Permission\PermissionService;
use App\Http\Resources\Admin\Permission\PermissionResource;
use App\Http\Resources\Admin\Permission\PermissionListResource;
use App\Http\Requests\Admin\Permission\PermissionRequest;

class PermissionController extends BaseController
{
    public function __construct(PermissionService $service)
    {
        parent::__construct($service, PermissionResource::class);
        $this->listResource = PermissionListResource::class;
        $this->storeRequestClass = PermissionRequest::class;
        $this->updateRequestClass = PermissionRequest::class;
    }
} 
