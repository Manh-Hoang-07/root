<?php

namespace App\Services\Admin\User;

use App\Repositories\User\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService extends BaseService
{
    public function __construct(UserRepository $repo)
    {
        parent::__construct($repo);
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            // Extract role_ids before creating user
            $roleIds = $data['role_ids'] ?? [];
            unset($data['role_ids']);
            
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            // Create user
            $user = $this->repo->create($data);
            
            // Always create profile (even if empty)
            $profileData = [
                'user_id' => $user->id,
                'name' => $data['name'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birthday' => $data['birthday'] ?? null,
                'address' => $data['address'] ?? null,
                'image' => $data['image'] ?? null,
                'about' => $data['about'] ?? null,
            ];
            
            $user->profile()->create($profileData);
            
            // Attach roles if provided
            if (!empty($roleIds)) {
                $user->roles()->attach($roleIds);
            }
            
            return $user->load(['profile', 'roles']);
        });
    }

    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            // Extract role_ids before updating user
            $roleIds = $data['role_ids'] ?? [];
            unset($data['role_ids']);
            
            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            // Update user
            $user = $this->repo->update($id, $data);
            
            // Update or create profile (always ensure profile exists)
            $profileData = [
                'name' => $data['name'] ?? null,
                'gender' => $data['gender'] ?? null,
                'birthday' => $data['birthday'] ?? null,
                'address' => $data['address'] ?? null,
                'image' => $data['image'] ?? null,
                'about' => $data['about'] ?? null,
            ];
            
            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                $profileData['user_id'] = $user->id;
                $user->profile()->create($profileData);
            }
            
            // Sync roles if provided
            $user->roles()->sync($roleIds);
            
            return $user->load(['profile', 'roles']);
        });
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

    /**
     * Tìm user theo ID với relationships
     */
    public function findById($id)
    {
        return $this->repo->find($id);
    }

    /**
     * Phân quyền cho user
     */
    public function assignRoles($id, $roleIds)
    {
        return DB::transaction(function () use ($id, $roleIds) {
            $user = $this->repo->find($id);
            
            if (!$user) {
                throw new \InvalidArgumentException('Không tìm thấy người dùng');
            }
            
            // Sync roles
            $user->roles()->sync($roleIds);
            
            return $user->load(['profile', 'roles']);
        });
    }

    /**
     * Tìm user theo ID với roles (cho modal phân quyền)
     */
    public function findByIdWithRoles($id)
    {
        return $this->repo->find($id, ['profile', 'roles']);
    }
} 