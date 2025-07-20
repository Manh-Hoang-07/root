<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;
use App\Services\BaseService;

class UserService extends BaseService
{
    public function __construct(UserRepository $repo)
    {
        parent::__construct($repo);
    }

    public function profile($id)
    {
        return $this->repo->profile($id);
    }

    public function updateProfile($id, $data)
    {
        return $this->repo->updateProfile($id, $data);
    }

    public function changePassword($id, $newPassword)
    {
        return $this->repo->changePassword($id, $newPassword);
    }
} 