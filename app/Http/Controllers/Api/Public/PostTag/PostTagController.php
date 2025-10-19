<?php

namespace App\Http\Controllers\Api\Public\PostTag;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Public\PostTag\PostTagService;

class PostTagController extends CrudController
{
    /**
     * @var PostTagService
     */
    protected $service;
    protected $indexRelations = [];
    protected $showRelations = [];

    public function __construct(PostTagService $service)
    {
        parent::__construct($service);
    }

    protected function getDefaultListFields(): array
    {
        return ['id', 'name', 'slug', 'status', 'created_at'];
    }

    protected function processFilters(array $filters, string $context = 'index'): array
    {
        $filters['status'] = 'active';
        return $filters;
    }
}
