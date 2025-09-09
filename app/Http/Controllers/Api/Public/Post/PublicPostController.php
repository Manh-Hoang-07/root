<?php

namespace App\Http\Controllers\Api\Public\Post;

use App\Http\Controllers\Api\BaseController;
use App\Services\Post\PostService;
use App\Http\Resources\Admin\Post\PostResource;
use Illuminate\Http\Request;

class PublicPostController extends BaseController
{
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

    protected function getOptimizedData(array $filters, int $perPage, string $context = 'index', bool $single = false)
    {
        $filters['published_only'] = true;
        return parent::getOptimizedData($filters, $perPage, $context, $single);
    }

    public function showBySlug($slug, Request $request)
    {
        $relations = $this->showRelations;
        $fields = $this->getDefaultShowFields();
        $item = $this->service->findBySlug($slug, $relations, $fields);
        if (!$item) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }
        return $this->successResponse($this->formatSingleData($item), 'Lấy thông tin thành công');
    }
}


