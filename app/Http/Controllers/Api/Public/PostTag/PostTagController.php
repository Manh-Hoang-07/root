<?php

namespace App\Http\Controllers\Api\Public\PostTag;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Public\PostTag\PostTagService;

class PostTagController extends CrudController
{
    protected $indexRelations = [];
    protected $showRelations = [];

    public function __construct(PostTagService $service)
    {
        parent::__construct($service);
    }

    protected function getDefaultListFields(): array
    {
        return ['id','name','slug','status','created_at'];
    }

    protected function getOptimizedData(array $filters, int $perPage, string $context = 'index', bool $single = false): array
    {
        $filters['status'] = 'active';
        return parent::getOptimizedData($filters, $perPage, $context, $single);
    }
}
