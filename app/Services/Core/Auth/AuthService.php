<?php

namespace App\Services\Core\Auth;

use App\Services\BaseService;
use App\Repositories\Auth\AuthRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserStatus;

class AuthService extends BaseService
{
    /**
     * @var AuthRepository
     */
    protected $repo;

    public function __construct(AuthRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Đăng nhập
     */
    public function login(array $data): array
    {
        $user = $this->repo->findOneBy(['email' => $data['email']]);
        if (!$user || !Hash::check($data['password'], $user['password'] ?? '')) {
            return [
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng.',
                'status' => 401
            ];
        }
        // Kiểm tra trạng thái user
        if ($user['status'] !== UserStatus::Active->value) {
            return [
                'success' => false,
                'message' => 'Tài khoản đã bị khóa hoặc không hoạt động.',
                'status' => 401
            ];
        }
        // Cập nhật last_login_at
        $this->repo->updateBy(['id' => $user['id'] ?? ''], ['last_login_at'=> now()]);
        // Tạo token với thời gian hết hạn 30 phút
        $user = $this->repo->arrayToModel($user, $this->repo->model());
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
            $user = $this->repo->create([
                'username' => $data['username'] ?? $data['email'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => UserStatus::Active->value
            ]);
            // Tạo profile
            if (!empty($user['id'])) {
                $this->repo->createProfile($user['id']);
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
        try {
            // Revoke tất cả tokens của user
            $user->tokens()->delete();
            return [
                'success' => true,
                'message' => 'Đăng xuất thành công.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Đăng xuất thất bại: ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken(?int $id = null): array
    {
        try {
            // If ID is not provided, get it from Auth
            if ($id === null) {
                $user = Auth::user();
                if (!$user) {
                    return [
                        'success' => false,
                        'message' => 'User not authenticated',
                        'status' => 401
                    ];
                }
                $id = $user->id;
            }
            
            // Revoke current token
            $user = $this->repo->arrayToModel($this->repo->find($id),$this->repo->model());
            $user->tokens()->delete();
            // Tạo token mới với thời gian hết hạn 24 giờ
            $token = $user->createToken('auth-token', ['*'], now()->addHours(24))->plainTextToken;
            return [
                'success' => true,
                'message' => 'Token refreshed successfully.',
                'data' => [
                    'token' => $token
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to refresh token: ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(int $id): array
    {
        try {
            // Load permissions và roles
            $fields = ['id', 'username', 'email', 'phone', 'status', 'email_verified_at', 'phone_verified_at', 'last_login_at', 'created_at', 'updated_at'];
            $user = $this->repo->arrayToModel($this->repo->find($id,fields:$fields),$this->repo->model());
            $user->load(['permissions']);
            return [
                'success' => true,
                'message' => 'Lấy thông tin user thành công',
                'data' => $user
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy thông tin user: ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(int $id, array $data): array
    {
        if(empty($id) || empty($data)){
            return [];
        }
        $user = $this->repo->find($id);
        var_dump($user);
        // Kiểm tra mật khẩu cũ
        if (!Hash::check($data['current_password'], $user['password'] ?? '')) {
            return [
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng.',
                'status' => 400
            ];
        }
        var_dump(111111);
        // Cập nhật mật khẩu mới
        $this->repo->updateBy(['id'=>$id],['password'=> Hash::make($data['password'])]);
        $user = $this->repo->arrayToModel($user,$this->repo->model());
        // Xóa tất cả token cũ để user phải đăng nhập lại
        $user->tokens()->delete();
        return [
            'success' => true,
            'message' => 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại.'
        ];
    }
} 