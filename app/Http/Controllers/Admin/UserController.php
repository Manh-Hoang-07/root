<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\Admin\UserService;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Enums\UserStatus;

class UserController extends BaseController
{
    public function __construct(UserService $userService)
    {
        parent::__construct($userService, UserResource::class);
    }

    // Danh sách tài khoản
    public function index(Request $request)
    {
        $users = $this->service->list($request->all());
        return UserResource::collection($users);
    }

    // Xem chi tiết tài khoản
    public function show($id, Request $request = null)
    {
        return parent::show($id, $request);
    }

    // Tạo mới tài khoản
    public function store(Request $request)
    {
        // Lấy đúng các trường cho user
        $userData = $request->only([
            'username', 'email', 'phone', 'password', 'status', 'email_verified_at', 'phone_verified_at', 'last_login_at'
        ]);
        $validated = $request->validate([
            'username' => 'nullable|string|max:50|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6',
            'status' => 'required|string',
            'email_verified_at' => 'nullable|date',
            'phone_verified_at' => 'nullable|date',
            'last_login_at' => 'nullable|date',
        ]);
        $userData['password'] = bcrypt($userData['password']);
        $user = $this->service->create($userData);
        // Luôn tạo profile, kể cả khi không có trường nào
        $profileData = $request->only(['name', 'gender', 'address', 'avatar']);
        $user->profile()->create($profileData);
        return new UserResource($user);
    }

    // Cập nhật tài khoản
    public function update(Request $request, $id)
    {
        // Validate tất cả các trường thuộc bảng users
        $request->validate([
            'username' => 'nullable|string|max:50|unique:users,username,'.$id,
            'email' => 'nullable|email|unique:users,email,'.$id,
            'phone' => 'nullable|string|max:20|unique:users,phone,'.$id,
            'password' => 'nullable|string|min:6',
            'status' => 'sometimes|string',
            'email_verified_at' => 'nullable|date',
            'phone_verified_at' => 'nullable|date',
            'last_login_at' => 'nullable|date',
        ]);
        // Lấy đúng các trường cho user
        $userData = $request->only([
            'username', 'email', 'phone', 'password', 'status', 'email_verified_at', 'phone_verified_at', 'last_login_at'
        ]);
        // Không xử lý password ở controller nữa, chỉ truyền sang service
        $user = $this->service->update($id, $userData);
        // Lấy đúng các trường cho profile
        $profileData = $request->only(['name', 'gender', 'address', 'avatar']);
        // Đảm bảo luôn truyền đủ key khi tạo profile mới
        $defaultProfile = [
            'name' => null,
            'gender' => null,
            'address' => null,
            'avatar' => null,
        ];
        if ($user->profile) {
            $user->profile->update($profileData);
        } else {
            $user->profile()->create(array_merge($defaultProfile, $profileData));
        }
        return new UserResource($user);
    }

    // Xóa tài khoản
    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['success' => true]);
    }

    // Đổi trạng thái hoạt động
    public function toggleStatus($id)
    {
        $user = $this->service->toggleStatus($id);
        return new UserResource($user);
    }

    // API trả về enum status cho frontend
    public function statuses()
    {
        $statuses = collect(UserStatus::cases())->map(function($case) {
            return [
                'value' => $case->value,
                'label' => $case->name
            ];
        })->values();
        return response()->json(['data' => $statuses]);
    }
}
