<?php
namespace App\Http\Controllers\Api\Admin\Permission;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Permission\PermissionRequest;
use App\Services\Admin\Permission\PermissionService;

class PermissionController extends CrudController
{
    protected $storeRequestClass = PermissionRequest::class;
    protected $updateRequestClass = PermissionRequest::class;
    protected $indexRelations = ['parent'];
    protected $showRelations = ['parent'];

    /**
     * @var PermissionService
     */
    protected $service;

    public function __construct(PermissionService $service)
    {
        parent::__construct($service);
    }
}
