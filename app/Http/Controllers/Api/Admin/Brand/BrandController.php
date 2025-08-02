<?php
namespace App\Http\Controllers\Api\Admin\Brand;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Brand\BrandService;
use App\Http\Resources\Admin\Brand\BrandResource;
use App\Http\Resources\Admin\Brand\BrandListResource;
use App\Http\Requests\Admin\Brand\BrandRequest;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    public function __construct(BrandService $service)
    {
        parent::__construct($service, BrandResource::class);
        $this->listResource = BrandListResource::class;
        $this->storeRequestClass = BrandRequest::class;
        $this->updateRequestClass = BrandRequest::class;
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

        $brands = $query->limit($limit)->get(['id', 'name']);

        return response()->json([
            'data' => $brands->map(function ($brand) {
                return [
                    'value' => $brand->id,
                    'label' => $brand->name
                ];
            })
        ]);
    }
} 
