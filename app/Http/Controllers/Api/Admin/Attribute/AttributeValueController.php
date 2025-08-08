<?php
namespace App\Http\Controllers\Api\Admin\Attribute;

use App\Http\Controllers\Api\BaseController;
use App\Services\Attribute\AttributeValueService;
use App\Http\Resources\Admin\Attribute\AttributeValueResource;
use App\Http\Resources\Admin\Attribute\AttributeValueListResource;
use App\Http\Requests\Admin\Attribute\AttributeValueRequest;

class AttributeValueController extends BaseController
{
    public function __construct(AttributeValueService $service)
    {
        parent::__construct($service, AttributeValueResource::class);
        $this->listResource = AttributeValueListResource::class;
        $this->storeRequestClass = AttributeValueRequest::class;
        $this->updateRequestClass = AttributeValueRequest::class;
        $this->indexRelations = ['attribute:id,name'];
        $this->showRelations = ['attribute'];
    }
} 
