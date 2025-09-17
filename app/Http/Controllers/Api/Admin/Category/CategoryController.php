<?php
namespace App\Http\Controllers\Api\Admin\Category;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Category\CategoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\Category\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function __construct(CategoryService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = CategoryRequest::class;
        $this->updateRequestClass = CategoryRequest::class;
        
        // Tối ưu: Chỉ load parent relation khi cần thiết
        $this->indexRelations = []; // Không load relations mặc định cho list
        
        // Load đầy đủ thông tin cho show/edit
        $this->showRelations = [
            'parent:id,name,slug,image', 
            'children:id,name,slug,parent_id,image,sort_order',
            'products:id,name,sku,image'
        ];
    }

} 
