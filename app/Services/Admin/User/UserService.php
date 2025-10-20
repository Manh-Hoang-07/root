<?php

namespace App\Services\Admin\User;

use App\Repositories\User\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService extends BaseService
{
    /**
     * @var UserRepository
     */
    protected $repo;

    public function __construct(UserRepository $repo)
    {
        parent::__construct($repo);
    }

    public function create($data): array
    {
        try {
            $result = DB::transaction(function () use ($data) {
                // Extract role_ids and profile before creating user
                $roleIds = $data['role_ids'] ?? [];
                $profileData = $data['profile'] ?? [];
                unset($data['role_ids'], $data['profile']);
                // Hash password if provided
                if (isset($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                }
                // Create user
                $user = $this->repo->create($data);
                // Get the model instance for relationships
                $userModel = $this->repo->getModel()->find($user['id']);
                // Create profile
                $profileData['user_id'] = $user['id'];
                $userModel->profile()->create($profileData);
                // Attach roles if provided
                if (!empty($roleIds)) {
                    $userModel->roles()->attach($roleIds);
                }
                return $user;
            });
            
            return [
                'success' => true,
                'message' => 'Tạo người dùng thành công',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể tạo người dùng',
                'data' => null
            ];
        }
    }

    public function update($id, $data): array
    {
        try {
            $result = DB::transaction(function () use ($id, $data) {
                // Extract role_ids and profile before updating user
                $roleIds = $data['role_ids'] ?? [];
                $profileData = $data['profile'] ?? [];
                unset($data['role_ids'], $data['profile']);
                // Hash password if provided
                if (isset($data['password']) && !empty($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                } else {
                    unset($data['password']);
                }
                // Update user
                $user = $this->repo->update($id, $data);
                if (!$user) {
                    return null;
                }
                // Get the model instance for relationships
                $userModel = $this->repo->getModel()->find($user['id']);
                // Update or create profile
                if (!empty($profileData)) {
                    if ($userModel->profile) {
                        $userModel->profile->update($profileData);
                    } else {
                        $profileData['user_id'] = $user['id'];
                        $userModel->profile()->create($profileData);
                    }
                }
                // Sync roles if provided
                $userModel->roles()->sync($roleIds);
                return $user;
            });
            
            if (!$result) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng',
                    'data' => null
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Cập nhật người dùng thành công',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật người dùng',
                'data' => null
            ];
        }
    }

    public function profile($id): array
    {
        $result = $this->repo->profile($id);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Lấy thông tin profile thành công',
                'data' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không tìm thấy thông tin profile',
                'data' => null
            ];
        }
    }

    public function updateProfile($id, $data): array
    {
        $result = $this->repo->updateProfile($id, $data);
        
        if ($result) {
            return [
                'success' => true,
                'message' => 'Cập nhật profile thành công',
                'data' => $result
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật profile',
                'data' => null
            ];
        }
    }

    public function changePassword($id, $newPassword): array
    {
        try {
            $result = $this->repo->changePassword($id, $newPassword);
            
            return [
                'success' => true,
                'message' => 'Đổi mật khẩu thành công',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể đổi mật khẩu',
                'data' => null
            ];
        }
    }

    /**
     * Phân quyền cho user
     */
    public function assignRoles($id, $roleIds): array
    {
        try {
            $result = DB::transaction(function () use ($id, $roleIds) {
                $user = $this->repo->find($id);
                if (!$user) {
                    return [
                        'success' => false,
                        'message' => 'Không tìm thấy người dùng',
                        'data' => null
                    ];
                }
                // Get the model instance for relationships
                $userModel = $this->repo->getModel()->find($user['id']);
                // Sync roles
                $userModel->roles()->sync($roleIds);
                return [
                    'success' => true,
                    'message' => 'Phân quyền thành công',
                    'data' => $user
                ];
            });
            
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể phân quyền',
                'data' => null
            ];
        }
    }
}
