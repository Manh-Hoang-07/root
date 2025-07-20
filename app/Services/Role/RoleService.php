<?php
namespace App\Services\Role;

use App\Repositories\Role\RoleRepository;
use App\Services\BaseService;

class RoleService extends BaseService
{
    public function __construct(RoleRepository $repo)
    {
        parent::__construct($repo);
    }
} 