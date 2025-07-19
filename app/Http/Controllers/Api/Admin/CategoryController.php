<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Services\CategoryService;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseController
{
    public function __construct(CategoryService $service)
    {
        parent::__construct($service, CategoryResource::class);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        Log::info('CategoryController store data', $data);
        Log::info('CategoryController request->input(image)', [$request->input('image')]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        $item = $this->service->create($data);
        return new CategoryResource($item);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        Log::info('CategoryController update data', $data);
        Log::info('CategoryController request->input(image)', [$request->input('image')]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }
        $item = $this->service->update($id, $data);
        return new CategoryResource($item);
    }

    // Có thể bổ sung các hàm filter, search... nếu cần
} 