<?php
namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Api\BaseController;
use App\Services\User\UserService;
use App\Http\Requests\Admin\User\UserRequest;
use App\Http\Requests\Admin\User\ChangePasswordRequest;
use App\Http\Requests\Admin\User\AssignRoleRequest;
use Illuminate\Http\Request;

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
    public function show($id, Request $request = null)
    {
        try {
            $user = $this->service->findById($id);
            
            if (!$user) {
                return $this->notFoundResponse('Không tìm thấy người dùng');
            }

            return $this->successResponse(
                $this->formatSingleData($user),
                'Lấy thông tin người dùng thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
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

    /**
     * Lấy thông tin user với roles (cho modal phân quyền)
     */
    public function showWithRoles($id)
    {
        try {
            $user = $this->service->findByIdWithRoles($id);
            
            if (!$user) {
                return $this->notFoundResponse('Không tìm thấy người dùng');
            }

            return $this->successResponse(
                $this->formatSingleData($user),
                'Lấy thông tin người dùng thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Phân quyền cho user
     */
    public function assignRoles(AssignRoleRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $result = $this->service->assignRoles($id, $data['role_ids']);
            
            return $this->successResponse(
                $result,
                'Phân quyền thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
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
