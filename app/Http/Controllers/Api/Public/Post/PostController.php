<?php

namespace App\Http\Controllers\Api\Public\Post;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Public\Post\PostService;
use Illuminate\Http\Request;

class PostController extends CrudController
{
    /**
     * @var PostService
     */
    protected $service;
    protected $indexRelations = ['categories:id,name,slug', 'tags:id,name,slug'];
    protected $showRelations = ['categories:id,name,slug', 'tags:id,name,slug', 'primaryCategory:id,name,slug'];

    public function __construct(PostService $service)
    {
        parent::__construct($service);
    }

    protected function getDefaultListFields(): array
    {
        return ['id', 'name', 'slug', 'image', 'status', 'published_at', 'created_at'];
    }

    protected function processFilters(array $filters, string $context = 'index'): array
    {
        $filters['published_only'] = true;
        return $filters;
    }

    public function showBySlug($slug, Request $request)
    {
        $relations = $this->showRelations;
        $fields = $this->getDefaultShowFields();
        $item = $this->service->findOneBy([
            'status' => 'published',
            'slug' => $slug,
        ], $relations, $fields);
        if (!$item) {
            return $this->apiResponse(false, null, 'Không tìm thấy dữ liệu', 404);
        }
        return $this->successResponseWithFormat($item, 'Lấy thông tin thành công');
    }
}
