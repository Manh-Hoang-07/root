<?php

namespace App\Services\Admin;

use App\Repositories\Admin\UserRepository;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function list($filters = [])
    {
        return $this->userRepo->all($filters);
    }

    public function find($id)
    {
        return $this->userRepo->find($id);
    }

    public function create($data)
    {
        return $this->userRepo->create($data);
    }

    public function update($id, $data)
    {
        return $this->userRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->userRepo->delete($id);
    }

    public function toggleStatus($id)
    {
        return $this->userRepo->toggleStatus($id);
    }
} 