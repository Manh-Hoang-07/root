<?php

namespace App\Services\Auth;

use App\Services\BaseService;
use App\Repositories\Auth\AuthRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserStatus;

class AuthService extends BaseService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Đăng nhập
     */
    public function login(array $data): array
    {
        $user = $this->authRepository->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng.',
                'status' => 401
            ];
        }

        // Kiểm tra trạng thái user
        if ($user->status !== UserStatus::Active) {
            return [
                'success' => false,
                'message' => 'Tài khoản đã bị khóa hoặc không hoạt động.',
                'status' => 401
            ];
        }

        // Cập nhật last_login_at
        $this->authRepository->updateLastLogin($user->id);

        // Tạo token với thời gian hết hạn 30 phút
        $token = $user->createToken('auth-token', ['*'], now()->addMinutes(60))->plainTextToken;

        return [
            'success' => true,
            'message' => 'Đăng nhập thành công.',
            'data' => [
                'token' => $token
            ]
        ];
    }

    /**
     * Đăng ký
     */
    public function register(array $data): array
    {
        try {
            $user = $this->authRepository->create([
                'username' => $data['username'] ?? $data['email'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => UserStatus::Active
            ]);

            // Tạo profile
            if (isset($data['name'])) {
                $this->authRepository->createProfile($user->id, [
                    'name' => $data['name']
                ]);
            }

            return [
                'success' => true,
                'message' => 'Đăng ký thành công.',
                'data' => [
                    'user' => $user
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Đăng ký thất bại: ' . $e->getMessage(),
                'status' => 422
            ];
        }
    }

    /**
     * Đăng xuất
     */
    public function logout(User $user): array
    {
        // Xóa tất cả token của user
        $user->tokens()->delete();

        return [
            'success' => true,
            'message' => 'Đăng xuất thành công.'
        ];
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(User $user, array $data): array
    {
        // Kiểm tra mật khẩu cũ
        if (!Hash::check($data['current_password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng.',
                'status' => 400
            ];
        }

        // Cập nhật mật khẩu mới
        $this->authRepository->updatePassword($user->id, Hash::make($data['password']));

        // Xóa tất cả token cũ để user phải đăng nhập lại
        $user->tokens()->delete();

        return [
            'success' => true,
            'message' => 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.'
        ];
    }
} 