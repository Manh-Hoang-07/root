<?php

namespace App\Services\Admin\Attribute;

use App\Repositories\Attribute\AttributeRepository;
use App\Services\BaseService;

class AttributeService extends BaseService
{
    public function __construct(AttributeRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Get attributes with their values
     */
    public function getAttributesWithValues(): array
    {
        return $this->repo->getAttributesWithValues();
    }
} 