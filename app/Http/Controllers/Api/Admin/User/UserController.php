<?php
namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\BaseController;
use App\Services\User\UserService;
use App\Http\Resources\Admin\User\UserResource;

class UserController extends BaseController
{
    public function __construct(UserService $service)
    {
        parent::__construct($service, UserResource::class);
    }
} 