<?php

namespace App\Http\Controllers\Api\Public\PostTag;

use App\Http\Controllers\Api\BaseController;
use App\Services\PostTag\PostTagService;

class PublicPostTagController extends BaseController
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

    protected function getOptimizedData(array $filters, int $perPage, string $context = 'index', bool $single = false)
    {
        $filters['status'] = 'active';
        return parent::getOptimizedData($filters, $perPage, $context, $single);
    }
}
