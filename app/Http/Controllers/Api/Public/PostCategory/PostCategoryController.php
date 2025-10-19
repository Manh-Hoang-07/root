<?php

namespace App\Http\Controllers\Api\Public\PostCategory;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Public\PostCategory\PostCategoryService;

class PostCategoryController extends CrudController
{
    /**
     * @var PostCategoryService
     */
    protected $service;
    protected $indexRelations = [];
    protected $showRelations = [];

    public function __construct(PostCategoryService $service)
    {
        parent::__construct($service);
    }

    protected function getDefaultListFields(): array
    {
        return ['id', 'name', 'slug', 'status', 'sort_order', 'created_at'];
    }

    protected function processFilters(array $filters, string $context = 'index'): array
    {
        $filters['status'] = 'active';
        return $filters;
    }
}
