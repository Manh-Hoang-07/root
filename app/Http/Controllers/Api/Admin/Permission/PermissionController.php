<?php
namespace App\Http\Controllers\Api\Admin\Permission;

use App\Http\Controllers\BaseController;
use App\Services\Permission\PermissionService;
use App\Http\Resources\Admin\Permission\PermissionResource;

class PermissionController extends BaseController
{
    public function __construct(PermissionService $service)
    {
        parent::__construct($service, PermissionResource::class);
    }
} 