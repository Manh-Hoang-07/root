<?php

namespace App\Http\Controllers\Api\Public\PostCategory;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Public\PostCategory\PostCategoryService;

class PostCategoryController extends CrudController
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

    protected function getOptimizedData(array $filters, int $perPage, string $context = 'index', bool $single = false): array
    {
        $filters['status'] = 'active';
        return parent::getOptimizedData($filters, $perPage, $context, $single);
    }
}
