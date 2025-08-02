<?php
namespace App\Http\Controllers\Api\Admin\Category;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Category\CategoryService;
use App\Http\Resources\Admin\Category\CategoryResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\Category\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function __construct(CategoryService $service)
    {
        parent::__construct($service, CategoryResource::class);
        $this->storeRequestClass = CategoryRequest::class;
        $this->updateRequestClass = CategoryRequest::class;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        return parent::index($request);
    }


} 
