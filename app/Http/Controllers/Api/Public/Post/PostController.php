<?php

namespace App\Http\Controllers\Api\Public\Post;

use App\Http\Controllers\Api\BaseController;
use App\Services\Public\Post\PostService;
use Illuminate\Http\Request;

class PostController extends BaseController
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
        return ['id','name','slug','image','status','published_at','created_at'];
    }

    protected function getOptimizedData(array $filters, int $perPage, string $context = 'index', bool $single = false): array
    {
        $filters['published_only'] = true;
        return parent::getOptimizedData($filters, $perPage, $context, $single);
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

