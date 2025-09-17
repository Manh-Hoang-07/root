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

    public function create($data): array
    {
        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return parent::create($data);
    }

    public function update($id, $data): ?array
    {
        if (!empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return parent::update($id, $data);
    }
    
    /**
     * Tìm danh mục theo ID với đầy đủ thông tin liên quan
     */
    public function find($id, $relations = [], $fields = ['*']): ?array
    {
        if (empty($relations)) {
            $relations = [
                'parent:id,name,slug,image',
                'children:id,name,slug,parent_id,image,sort_order',
                'products:id,name,sku,image'
            ];
        }
        
        return parent::find($id, $relations, $fields);
    }
} 