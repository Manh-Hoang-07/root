<?php
namespace App\Http\Controllers\Api\Admin\Role;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Role\RoleRequest;
use App\Services\Admin\Role\RoleService;

class RoleController extends CrudController
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
