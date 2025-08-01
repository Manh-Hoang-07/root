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

    public function search(Request $request)
    {
        $search = $request->get('search', '');
        $id = $request->get('id');
        $ids = $request->get('ids');
        $limit = min($request->get('limit', 10), 100); // Max 100 items

        $query = app($this->service->getRepo()->model())->query();

        if ($id) {
            $query->where('id', $id);
        } elseif ($ids) {
            $idsArray = explode(',', $ids);
            $query->whereIn('id', $idsArray);
        } elseif ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->limit($limit)->get(['id', 'name']);

        return response()->json([
            'data' => $categories->map(function ($category) {
                return [
                    'value' => $category->id,
                    'label' => $category->name
                ];
            })
        ]);
    }
} 
