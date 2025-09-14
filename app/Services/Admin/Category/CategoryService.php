<?php
namespace App\Services\Admin\Category;

use App\Repositories\Category\CategoryRepository;
use App\Services\BaseService;
use Illuminate\Support\Str;

class CategoryService extends BaseService
{
    public function __construct(CategoryRepository $repo)
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