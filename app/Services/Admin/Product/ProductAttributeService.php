<?php

namespace App\Services\Admin\Product;

use App\Services\BaseService;
use App\Repositories\Product\ProductAttributeRepository;

class ProductAttributeService extends BaseService
{
    public function __construct(ProductAttributeRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Update attribute status
     */
    public function updateStatus($id, string $status): ?array
    {
        return $this->repo->updateStatus($id, $status);
    }


    /**
     * Override create to ensure slug generation
     */
    public function create($data): array
    {
        $data = $this->ensureSlug($data);
        return parent::create($data);
    }

    /**
     * Override update to ensure slug generation
     */
    public function update($id, $data): ?array
    {
        $data = $this->ensureSlug($data);
        return parent::update($id, $data);
    }
}
