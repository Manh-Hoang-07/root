<?php

namespace App\Http\Controllers\Api\User\User;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        parent::__construct($authService);
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
        
        // Format data giống như AuthResource
        $userData = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'name' => $user->profile?->name ?? $user->username,
            'phone' => $user->phone,
            'status' => $user->status,
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ];
        
        return $this->successResponseWithFormat($userData, 'Lấy thông tin user thành công.');
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