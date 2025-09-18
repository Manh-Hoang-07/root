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
}