<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ResponseTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Đăng nhập
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());
        
        if ($result['success']) {
            $response = $this->successResponse(
                $result['data'],
                $result['message']
            );
            
            // Set cookie với token
            if (isset($result['data']['token'])) {
                $domain = request()->getHost() === 'localhost' ? 'localhost' : null;
                $response->cookie('auth_token', $result['data']['token'], 60, '/', $domain, false, false);
            }
            
            return $response;
        }

        return $this->errorResponse(
            $result['message'],
            $result['status'] ?? 401
        );
    }

    /**
     * Đăng ký
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());
        
        if ($result['success']) {
            return $this->createdResponse(
                $result['data'],
                $result['message']
            );
        }

        return $this->errorResponse(
            $result['message'],
            $result['status'] ?? 422
        );
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return $this->unauthorizedResponse();
        }
        
        // Lấy role đầu tiên của user (nếu có)
        $role = $user->roles->first();
        $roleName = $role ? $role->name : null;
        
        return $this->successResponse([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $roleName,
            'status' => $user->status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ], 'Thông tin user');
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout($request->user());
        
        $response = $this->successResponse(
            null,
            $result['message']
        );
        
        // Xóa cookie auth_token
        $response->cookie('auth_token', '', -1, '/', null, false, false);
        
        return $response;
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return $this->unauthorizedResponse();
            }

            // Sử dụng AuthService để refresh token
            $result = $this->authService->refreshToken($user);
            
            if ($result['success']) {
                $response = $this->successResponse(
                    $result['data'],
                    $result['message']
                );
                
                // Set cookie với token mới
                if (isset($result['data']['token'])) {
                    $domain = request()->getHost() === 'localhost' ? 'localhost' : null;
                    $response->cookie('auth_token', $result['data']['token'], 1440, '/', $domain, false, false);
                }
                
                return $response;
            }

            return $this->errorResponse(
                $result['message'],
                $result['status'] ?? 500
            );
            
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to refresh token');
        }
    }
} 