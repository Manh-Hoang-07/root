<?php
namespace App\Http\Controllers\Api\Admin\Permission;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Permission\PermissionService;
use App\Http\Requests\Admin\Permission\PermissionRequest;

class PermissionController extends BaseController
{
    protected static $serviceClass = PermissionService::class;
    protected $storeRequestClass = PermissionRequest::class;
    protected $updateRequestClass = PermissionRequest::class;
} 
