<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function model(): string
    {
        return User::class;
    }
    
    /**
     * Ghi đè getSearchableFields để thêm các trường tìm kiếm cho User
     */
    protected function getSearchableFields(): array
    {
        return ['username', 'email', 'name'];
    }

    public function changePassword($id, string $newPassword): array
    {
        $user = $this->model->find($id);
        if (!$user) {
            throw new \Exception('User not found');
        }
        $user->password = Hash::make($newPassword);
        $user->save();
        return $user->toArray();
    }

    /**
     * Get user profile
     */
    public function profile($id): ?array
    {
        $user = $this->model->with('profile')->find($id);
        return $user ? $user->toArray() : null;
    }

    /**
     * Update user profile
     */
    public function updateProfile($id, array $data): ?array
    {
        $user = $this->model->find($id);
        if (!$user) {
            return null;
        }
        if ($user->profile) {
            $user->profile->update($data);
        } else {
            $data['user_id'] = $user->id;
            $user->profile()->create($data);
        }
        return $user->load('profile')->toArray();
    }
} 