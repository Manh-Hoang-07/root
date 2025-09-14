<?php

namespace App\Http\Controllers\Api\Admin\Attribute;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Attribute\AttributeService;
use App\Http\Requests\Admin\Attribute\AttributeRequest;

class AttributeController extends BaseController
{
    public function __construct(AttributeService $service)
    {
        parent::__construct($service);
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
            return $this->successResponseWithFormat($attributes, 'Lấy danh sách thành công');
        }
        
        // Nếu không, sử dụng method của BaseController
        return parent::index($request);
    }
} 
