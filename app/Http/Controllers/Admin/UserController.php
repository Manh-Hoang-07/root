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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'status' => 'required|string',
        ]);
        $data['password'] = bcrypt($data['password']);
        $user = $this->service->create($data);
        return new UserResource($user);
    }

    // Cập nhật tài khoản
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6',
            'role' => 'sometimes|string',
            'status' => 'sometimes|string',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user = $this->service->update($id, $data);
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
