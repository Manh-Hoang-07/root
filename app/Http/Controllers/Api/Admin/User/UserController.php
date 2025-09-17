<?php
namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\User\UserService;
use App\Http\Requests\Admin\User\UserRequest;
use App\Http\Requests\Admin\User\ChangePasswordRequest;
use App\Http\Requests\Admin\User\AssignRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{
    public function __construct(UserService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = UserRequest::class;
        $this->updateRequestClass = UserRequest::class;
    }

    /**
     * Lấy thông tin chi tiết user
     */
    public function show($id, ?Request $request = null): JsonResponse
    {
        try {
            $user = $this->service->findById($id);
            
            if (!$user) {
                return $this->apiResponse(false, null, 'Không tìm thấy người dùng', 404);
            }

            return $this->successResponseWithFormat($user, 'Lấy thông tin người dùng thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request, $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->service->changePassword($id, $data['password']);
            
            return $this->apiResponse(true, $result, 'Mật khẩu đã được thay đổi thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Lấy thông tin user với roles (cho modal phân quyền)
     */
    public function showWithRoles($id): JsonResponse
    {
        try {
            $user = $this->service->findByIdWithRoles($id);
            
            if (!$user) {
                return $this->apiResponse(false, null, 'Không tìm thấy người dùng', 404);
            }

            return $this->successResponseWithFormat($user, 'Lấy thông tin người dùng thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Phân quyền cho user
     */
    public function assignRoles(AssignRoleRequest $request, $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $result = $this->service->assignRoles($id, $data['role_ids']);
            
            return $this->apiResponse(true, $result, 'Phân quyền thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }

    /**
     * Override default search fields for User model
     */
    protected function getSearchFields(): array
    {
        return ['id', 'username', 'email'];
    }

} 
