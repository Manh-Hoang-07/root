<?php
namespace App\Http\Controllers\Api\Admin\Category;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Category\CategoryService;
use App\Http\Requests\Admin\Category\CategoryRequest;

class CategoryController extends BaseController
{
    protected $storeRequestClass = CategoryRequest::class;
    protected $updateRequestClass = CategoryRequest::class;
    protected $showRelations = [
        'parent:id,name,slug,image', 
        'children:id,name,slug,parent_id,image,sort_order',
        'products:id,name,sku,image'
    ];
    
    /**
     * @var CategoryService
     */
    protected $service;

    public function __construct(CategoryService $service)
    {
        parent::__construct($service);
    }
} 
