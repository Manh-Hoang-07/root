<?php

namespace App\Http\Controllers\Api\User\User;

use App\Http\Controllers\Api\BaseController;
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
        parent::__construct($authService, AuthResource::class);
        $this->authService = $authService;
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
        return $this->apiResponse(false, null, 'Không tìm thấy thông tin user.', 401);
        }
        
        return $this->successResponseWithFormat($user, 'Lấy thông tin user thành công.');
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
            return $this->apiResponse(true, null, $result['message']);
        }

        return $this->apiResponse(false, null, $result['message'], $result['status'] ?? 400);
    }
} 