<?php
namespace App\Http\Controllers\Attribute;

use App\Http\Controllers\BaseController;
use App\Services\Attribute\AttributeValueService;
use App\Http\Resources\AttributeValueResource;

class AttributeValueController extends BaseController
{
    public function __construct(AttributeValueService $service)
    {
        parent::__construct($service, AttributeValueResource::class);
    }
} 