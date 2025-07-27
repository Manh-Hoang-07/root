<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
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
                $response->cookie('auth_token', $result['data']['token'], 30); // 30 phút
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
            return $this->successResponse(
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
     * Đăng xuất
     */
    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout($request->user());
        
        return $this->successResponse(
            null,
            $result['message']
        );
    }





    /**
     * Success response
     */
    protected function successResponse($data = null, string $message = 'Thành công', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message = 'Có lỗi xảy ra', int $status = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse($errors, string $message = 'Dữ liệu không hợp lệ'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }
} 