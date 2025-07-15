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

    public function update($id, $data)
    {
        if (isset($data['password']) && $data['password'] !== '' && $data['password'] !== null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        return parent::update($id, $data);
    }

    public function create($data)
    {
        if (isset($data['password']) && $data['password'] !== '' && $data['password'] !== null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        return parent::create($data);
    }
} 