<?php
namespace App\Http\Controllers\Api\Admin\Role;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Role\RoleService;
use App\Http\Requests\Admin\Role\RoleRequest;

class RoleController extends BaseController
{
    protected static $serviceClass = RoleService::class;
    protected $storeRequestClass = RoleRequest::class;
    protected $updateRequestClass = RoleRequest::class;
    protected $showRelations = [
        'permissions:id,name,guard_name'
    ];
} 
