<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository
{
    public function profile($id)
    {
        return User::findOrFail($id);
    }

    public function updateProfile($id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function changePassword($id, $newPassword)
    {
        $user = User::findOrFail($id);
        $user->password = bcrypt($newPassword);
        $user->save();
        return $user;
    }
} 