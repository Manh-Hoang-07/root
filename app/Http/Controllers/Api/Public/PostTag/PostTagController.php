<?php

namespace App\Http\Controllers\Api\Public\PostTag;

use App\Http\Controllers\Api\BaseController;
use App\Services\Public\PostTag\PostTagService;

class PostTagController extends BaseController
{
    protected static $serviceClass = PostTagService::class;
    protected $indexRelations = [];
    protected $showRelations = [];

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
