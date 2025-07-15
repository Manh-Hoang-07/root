<?php

namespace App\Services\Admin;

use App\Repositories\Admin\UserRepository;
use App\Services\BaseService;

class UserService extends BaseService
{
    public function __construct(UserRepository $userRepo)
    {
        parent::__construct($userRepo);
    }

    public function toggleStatus($id)
    {
        return $this->repo->toggleStatus($id);
    }
} 