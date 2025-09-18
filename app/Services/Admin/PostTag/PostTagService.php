<?php

namespace App\Services\Admin\PostTag;

use App\Services\BaseService;
use App\Repositories\PostTag\PostTagRepository;

class PostTagService extends BaseService
{
    public function __construct(PostTagRepository $repo)
    {
        parent::__construct($repo);
    }

    // Deprecated: use BaseService::findOneBy(['slug' => $slug], ...)

    public function create($data): array
    {
        $data = $this->ensureSlug($data);
        return parent::create($data);
    }

    public function update($id, $data): ?array
    {
        $data = $this->ensureSlug($data);
        return parent::update($id, $data);
    }
}


