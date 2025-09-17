<?php

namespace App\Http\Controllers\Api\Admin\Attribute;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Attribute\AttributeService;
use App\Http\Requests\Admin\Attribute\AttributeRequest;
use Illuminate\Http\JsonResponse;

class AttributeController extends BaseController
{
    protected static $serviceClass = AttributeService::class;
    protected $storeRequestClass = AttributeRequest::class;
    protected $updateRequestClass = AttributeRequest::class;

    /**
     * Get attributes with their values
     */
    public function index(Request $request): JsonResponse
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
