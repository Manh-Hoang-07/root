<?php
namespace App\Http\Controllers\Api\Admin\Role;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Role\RoleService;
use App\Http\Requests\Admin\Role\RoleRequest;

class RoleController extends BaseController
{
    protected $storeRequestClass = RoleRequest::class;
    protected $updateRequestClass = RoleRequest::class;
    protected $showRelations = [
        'permissions:id,name,guard_name'
    ];
    
    /**
     * @var RoleService
     */
    protected $service;

    public function __construct(RoleService $service)
    {
        parent::__construct($service);
    }

} 
