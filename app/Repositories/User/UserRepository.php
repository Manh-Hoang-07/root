<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function model()
    {
        return User::class;
    }

    public function changePassword($id, $newPassword)
    {
        $user = $this->find($id);
        if (!$user) {
            throw new \Exception('User not found');
        }
        
        $user->password = Hash::make($newPassword);
        $user->save();
        
        return $user;
    }
} 