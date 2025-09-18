<?php
namespace App\Http\Controllers\Api\Admin\Permission;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Permission\PermissionService;
use App\Http\Requests\Admin\Permission\PermissionRequest;

class PermissionController extends BaseController
{
    /**
     * @var PermissionService
     */
    protected $service;

    public function __construct(PermissionService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = PermissionRequest::class;
        $this->updateRequestClass = PermissionRequest::class;
    }
} 
