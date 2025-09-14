<?php

namespace App\Http\Controllers\Api\Public\PostCategory;

use App\Http\Controllers\Api\BaseController;
use App\Services\Public\PostCategory\PostCategoryService;

class PublicPostCategoryController extends BaseController
{
    protected $indexRelations = [];
    protected $showRelations = [];

    public function __construct(PostCategoryService $service)
    {
        parent::__construct($service);
    }

    protected function getDefaultListFields(): array
    {
        return ['id','name','slug','status','sort_order','created_at'];
    }

    protected function getOptimizedData(array $filters, int $perPage, string $context = 'index', bool $single = false)
    {
        $filters['status'] = 'active';
        return parent::getOptimizedData($filters, $perPage, $context, $single);
    }
}
