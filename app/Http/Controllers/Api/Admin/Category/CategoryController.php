<?php
namespace App\Http\Controllers\Api\Admin\Category;

use App\Http\Controllers\BaseController;
use App\Services\Category\CategoryService;
use App\Http\Resources\Admin\Category\CategoryResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseController
{
    public function __construct(CategoryService $service)
    {
        parent::__construct($service, CategoryResource::class);
    }

    public function index(\Illuminate\Http\Request $request)
    {
        DB::enableQueryLog();
        $start = microtime(true);
        $response = parent::index($request);
        $queries = DB::getQueryLog();
        $duration = round((microtime(true) - $start) * 1000, 2);
        Log::info('CategoryController@index debug', [
            'duration_ms' => $duration,
            'queries' => $queries,
            'request' => $request->all()
        ]);
        return $response;
    }
} 