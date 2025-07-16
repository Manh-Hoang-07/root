<?php

namespace App\Services;

use App\Repositories\PermissionRepository;

class PermissionService extends BaseService
{
    public function __construct(PermissionRepository $permissionRepo)
    {
        parent::__construct($permissionRepo);
    }
    // Custom method nếu cần
} 