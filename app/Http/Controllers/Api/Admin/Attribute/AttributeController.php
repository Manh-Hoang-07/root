<?php
namespace App\Http\Controllers\Api\Admin\Attribute;

use App\Http\Controllers\BaseController;
use App\Services\Attribute\AttributeService;
use App\Http\Resources\Admin\Attribute\AttributeResource;

class AttributeController extends BaseController
{
    public function __construct(AttributeService $service)
    {
        parent::__construct($service, AttributeResource::class);
    }
} 