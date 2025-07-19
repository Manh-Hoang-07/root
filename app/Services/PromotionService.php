<?php

namespace App\Services;

use App\Repositories\PromotionRepository;

class PromotionService extends BaseService
{
    public function __construct(PromotionRepository $repository)
    {
        parent::__construct($repository);
    }
} 