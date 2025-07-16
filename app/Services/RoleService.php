<?php

namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService extends BaseService
{
    public function __construct(RoleRepository $roleRepo)
    {
        parent::__construct($roleRepo);
    }
    // Custom method nếu cần
} 