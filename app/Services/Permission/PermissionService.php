<?php
namespace App\Services\Permission;

use App\Repositories\Permission\PermissionRepository;
use App\Services\BaseService;

class PermissionService extends BaseService
{
    public function __construct(PermissionRepository $repo)
    {
        parent::__construct($repo);
    }
} 