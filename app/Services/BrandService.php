<?php

namespace App\Services;

use App\Repositories\BrandRepository;
use Illuminate\Support\Str;

class BrandService extends BaseService
{
    public function __construct(BrandRepository $repository)
    {
        parent::__construct($repository);
    }

    public function create($data)
    {
        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return parent::create($data);
    }

    public function update($id, $data)
    {
        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return parent::update($id, $data);
    }
    // Có thể bổ sung các hàm filter, search... nếu cần
} 