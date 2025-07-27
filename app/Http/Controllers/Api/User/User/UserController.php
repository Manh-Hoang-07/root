<?php

namespace App\Http\Controllers\Api\User\User;

use App\Http\Controllers\Api\User\BaseController;
use App\Http\Resources\Auth\AuthResource;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return $this->errorResponse(
                'Không tìm thấy thông tin user.',
                401
            );
        }
        
        return $this->successResponse(
            new AuthResource($user),
            'Lấy thông tin user thành công.'
        );
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $result = $this->authService->changePassword(
            $request->user(),
            $request->validated()
        );
        
        if ($result['success']) {
            return $this->successResponse(
                null,
                $result['message']
            );
        }

        return $this->errorResponse(
            $result['message'],
            $result['status'] ?? 400
        );
    }
} 