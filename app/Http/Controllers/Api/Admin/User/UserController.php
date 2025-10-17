<?php
namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\User\AssignRoleRequest;
use App\Http\Requests\Admin\User\ChangePasswordRequest;
use App\Http\Requests\Admin\User\StatusUpdateRequest;
use App\Http\Requests\Admin\User\UserRequest;
use App\Services\Admin\User\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends CrudController
{
    protected $storeRequestClass = UserRequest::class;
    protected $updateRequestClass = UserRequest::class;
    protected $statusUpdateRequestClass = StatusUpdateRequest::class;
    protected $showRelations = [
        'profile:id,user_id,name,address,gender,birthday,image,about',
        'roles:id,name,guard_name',
        'permissions:id,name,guard_name'
    ];

    /**
     * @var UserService
     */
    protected $service;

    public function __construct(UserService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'username', 'email'];
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

}
