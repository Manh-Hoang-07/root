<?php

namespace App\Repositories\Admin;

use App\Models\User;
use App\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new User());
    }

    public function toggleStatus($id)
    {
        $user = $this->find($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        return $user;
    }
} 