<?php
namespace App\Http\Controllers\Api\Admin\Category;

use App\Http\Controllers\Api\BaseController;
use App\Services\Category\CategoryService;
use App\Http\Resources\Admin\Category\CategoryResource;
use App\Http\Resources\Admin\Category\CategoryListResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\Category\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function __construct(CategoryService $service)
    {
        parent::__construct($service, CategoryResource::class);
        $this->listResource = CategoryListResource::class;
        $this->storeRequestClass = CategoryRequest::class;
        $this->updateRequestClass = CategoryRequest::class;
        
        // Chỉ load parent relation cho list view
        $this->indexRelations = ['parent:id,name'];
        
        // Load đầy đủ thông tin cho show/edit
        $this->showRelations = ['parent', 'children'];
    }

    public function index(\Illuminate\Http\Request $request)
    {
        return parent::index($request);
    }


} 
