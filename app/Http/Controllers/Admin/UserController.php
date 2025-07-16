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
        // Nhận đủ các trường cho profile
        $profileData = $request->only(['name', 'gender', 'address', 'avatar', 'birthday', 'about']);
        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
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
        // Nhận đủ các trường cho profile
        $profileData = $request->only(['name', 'gender', 'address', 'avatar', 'birthday', 'about']);
        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        // Đảm bảo luôn truyền đủ key khi tạo profile mới
        $defaultProfile = [
            'name' => null,
            'gender' => null,
            'address' => null,
            'avatar' => null,
            'birthday' => null,
            'about' => null,
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
    public function toggleStatus(Request $request, $id)
    {
        $user = $this->service->find($id);
        $status = $request->input('status');
        $allowed = ['active', 'inactive', 'suspended'];
        if ($status && in_array($status, $allowed)) {
            $user->status = $status;
            $user->save();
            return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
        } else {
            return response()->json(['success' => false, 'message' => 'Trạng thái không hợp lệ'], 400);
        }
    }

    // Đổi mật khẩu tài khoản
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);
        $user = $this->service->find($id);
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['success' => true, 'message' => 'Đổi mật khẩu thành công']);
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
