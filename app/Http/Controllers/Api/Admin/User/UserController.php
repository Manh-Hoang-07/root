<?php
namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\BaseController;
use App\Services\User\UserService;
use App\Http\Resources\Admin\User\UserResource;
use App\Http\Requests\Admin\User\UserRequest;

class UserController extends BaseController
{
    public function __construct(UserService $service)
    {
        parent::__construct($service, UserResource::class);
        $this->storeRequestClass = UserRequest::class;
        $this->updateRequestClass = UserRequest::class;
    }
} 