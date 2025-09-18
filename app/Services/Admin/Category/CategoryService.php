<?php
namespace App\Services\Admin\Category;

use App\Repositories\Category\CategoryRepository;
use App\Services\BaseService;

class CategoryService extends BaseService
{
    public function __construct(CategoryRepository $repo)
    {
        parent::__construct($repo);
    }

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