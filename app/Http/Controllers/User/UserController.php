<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Services\User\UserService;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(UserService $service)
    {
        parent::__construct($service, UserResource::class);
    }

    // Lấy thông tin cá nhân
    public function profile(Request $request)
    {
        $user = $request->user();
        return new UserResource($this->service->profile($user->id));
    }

    // Cập nhật thông tin cá nhân
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
        ]);
        $user = $this->service->updateProfile($user->id, $data);
        return new UserResource($user);
    }

    // Đổi mật khẩu
    public function changePassword(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);
        $user = $this->service->changePassword($user->id, $data['password']);
        return response()->json(['success' => true]);
    }
}
