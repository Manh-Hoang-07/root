<?php

namespace App\Services\Public\Product;

use App\Services\BaseService;
use App\Repositories\Product\ProductRepository;

class ProductService extends BaseService
{
    /**
     * @var ProductRepository
     */
    protected $repo;

    public function __construct(ProductRepository $repo)
    {
        parent::__construct($repo);
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
    public function update($id, $data): array
    {
        $data = $this->ensureSlug($data);
        return parent::update($id, $data);
    }
}
