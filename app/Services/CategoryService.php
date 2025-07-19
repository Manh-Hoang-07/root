<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CategoryService extends BaseService
{
    public function __construct(CategoryRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create($data)
    {
        Log::info('Category data before save (1111111111)', $data);
        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        // Chỉ lưu link ảnh (URL), không xử lý file upload nữa
        if (isset($data['image']) && is_object($data['image'])) {
            unset($data['image']);
        }
        if (empty($data['image'])) {
            $data['image'] = '';
        }
        Log::info('Category data before save (create)', $data);
        return parent::create($data);
    }

    public function update($id, $data)
    {
        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        // Chỉ lưu link ảnh (URL), không xử lý file upload nữa
        if (isset($data['image']) && is_object($data['image'])) {
            unset($data['image']);
        }
        if (empty($data['image'])) {
            $data['image'] = '';
        }
        Log::info('Category data before save (update)', $data);
        return parent::update($id, $data);
    }

    // Có thể bổ sung các hàm filter, search... nếu cần
} 