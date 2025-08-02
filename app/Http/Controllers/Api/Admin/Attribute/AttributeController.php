<?php

namespace App\Http\Controllers\Api\Admin\Attribute;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Attribute\AttributeService;
use App\Http\Resources\Admin\Attribute\AttributeResource;
use App\Http\Resources\Admin\Attribute\AttributeListResource;
use App\Http\Requests\Admin\Attribute\AttributeRequest;

class AttributeController extends BaseController
{
    public function __construct(AttributeService $service)
    {
        parent::__construct($service, AttributeResource::class);
        $this->listResource = AttributeListResource::class;
        $this->storeRequestClass = AttributeRequest::class;
        $this->updateRequestClass = AttributeRequest::class;
    }

    /**
     * Get attributes with their values
     */
    public function index(Request $request)
    {
        // Nếu có parameter relations=values, load với attributeValues
        if ($request->get('relations') === 'values' || $request->get('with_values')) {
            $attributes = $this->service->getAttributesWithValues();
            return AttributeResource::collection($attributes);
        }
        
        // Nếu không, sử dụng method của BaseController
        return parent::index($request);
    }
} 
