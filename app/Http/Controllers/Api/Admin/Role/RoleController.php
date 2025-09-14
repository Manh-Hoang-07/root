<?php
namespace App\Http\Controllers\Api\Admin\Role;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Role\RoleService;
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

} 
