<?php
namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\BaseController;
use App\Services\User\UserService;
use App\Http\Resources\Admin\User\UserResource;
use App\Http\Requests\Admin\User\UserRequest;
use App\Http\Requests\Admin\User\ChangePasswordRequest;

class UserController extends BaseController
{
    public function __construct(UserService $service)
    {
        parent::__construct($service, UserResource::class);
        $this->storeRequestClass = UserRequest::class;
        $this->updateRequestClass = UserRequest::class;
    }

    public function changePassword(ChangePasswordRequest $request, $id)
    {
        $data = $request->validated();
        $result = $this->service->changePassword($id, $data['password']);
        
        return response()->json([
            'message' => 'Mật khẩu đã được thay đổi thành công.',
            'data' => $result
        ]);
    }
} 