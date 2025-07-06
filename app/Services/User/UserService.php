<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function profile($id)
    {
        return $this->userRepo->profile($id);
    }

    public function updateProfile($id, $data)
    {
        return $this->userRepo->updateProfile($id, $data);
    }

    public function changePassword($id, $newPassword)
    {
        return $this->userRepo->changePassword($id, $newPassword);
    }
} 