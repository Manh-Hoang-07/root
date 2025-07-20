<?php
namespace App\Http\Controllers\Api\Admin\Role;

use App\Http\Controllers\BaseController;
use App\Services\Role\RoleService;
use App\Http\Resources\Admin\RoleResource;

class RoleController extends BaseController
{
    public function __construct(RoleService $service)
    {
        parent::__construct($service, RoleResource::class);
    }
} 