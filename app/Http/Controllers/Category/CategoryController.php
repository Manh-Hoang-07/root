<?php
namespace App\Http\Controllers\Category;

use App\Http\Controllers\BaseController;
use App\Services\Category\CategoryService;
use App\Http\Resources\CategoryResource;

class CategoryController extends BaseController
{
    public function __construct(CategoryService $service)
    {
        parent::__construct($service, CategoryResource::class);
    }
} 