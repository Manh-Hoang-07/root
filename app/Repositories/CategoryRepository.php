<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function create(array $data)
    {
        Log::info('CategoryRepository create data', $data);
        return parent::create($data);
    }

    public function update($id, array $data)
    {
        Log::info('CategoryRepository update data', $data);
        return parent::update($id, $data);
    }
    // Có thể bổ sung các hàm filter, search... nếu cần
} 