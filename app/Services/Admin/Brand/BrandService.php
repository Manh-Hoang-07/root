<?php
namespace App\Services\Admin\Brand;

use App\Repositories\Brand\BrandRepository;
use App\Services\BaseService;
use Illuminate\Support\Str;

class BrandService extends BaseService
{
    public function __construct(BrandRepository $repo)
    {
        parent::__construct($repo);
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
} 