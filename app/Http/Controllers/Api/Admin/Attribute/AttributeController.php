<?php

namespace App\Http\Controllers\Api\Admin\Attribute;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Attribute\AttributeService;
use App\Http\Requests\Admin\Attribute\AttributeRequest;

class AttributeController extends BaseController
{
    protected $storeRequestClass = AttributeRequest::class;
    protected $updateRequestClass = AttributeRequest::class;
    
    /**
     * Định nghĩa các relation fields được tối ưu
     */
    protected $relationFields = [
        'attributeValues' => 'attributeValues:id,attribute_id,value',
        'values' => 'values:id,attribute_id,value'
    ];
    
    /**
     * @var AttributeService
     */
    protected $service;

    public function __construct(AttributeService $service)
    {
        parent::__construct($service);
    }

} 
