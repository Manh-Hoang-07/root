<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepository;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;

class AuthRepository extends BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the model class name
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * Tìm user theo email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Tìm user theo username
     */
    public function findByUsername(string $username): ?User
    {
        return $this->model->where('username', $username)->first();
    }

    /**
     * Cập nhật thời gian đăng nhập cuối
     */
    public function updateLastLogin(int $userId): bool
    {
        return $this->model->where('id', $userId)->update([
            'last_login_at' => now()
        ]);
    }

    /**
     * Cập nhật mật khẩu
     */
    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        return $this->model->where('id', $userId)->update([
            'password' => $hashedPassword
        ]);
    }

    /**
     * Tạo profile cho user
     */
    public function createProfile(int $userId, array $data): Profile
    {
        return Profile::create([
            'user_id' => $userId,
            'name' => $data['name']
        ]);
    }

    /**
     * Cập nhật profile
     */
    public function updateProfile(int $userId, array $data): bool
    {
        return Profile::where('user_id', $userId)->update($data);
    }
} 