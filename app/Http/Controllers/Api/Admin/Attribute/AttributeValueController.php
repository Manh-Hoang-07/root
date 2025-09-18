<?php
namespace App\Http\Controllers\Api\Admin\Attribute;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Attribute\AttributeValueService;
use App\Http\Requests\Admin\Attribute\AttributeValueRequest;

class AttributeValueController extends BaseController
{
    protected $storeRequestClass = AttributeValueRequest::class;
    protected $updateRequestClass = AttributeValueRequest::class;
    protected $indexRelations = ['attribute:id,name'];
    protected $showRelations = ['attribute'];
    
    /**
     * @var AttributeValueService
     */
    protected $service;

    public function __construct(AttributeValueService $service)
    {
        parent::__construct($service);
    }
} 
