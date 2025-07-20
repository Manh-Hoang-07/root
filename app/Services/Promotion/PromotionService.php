<?php
namespace App\Services\Promotion;

use App\Repositories\Promotion\PromotionRepository;
use App\Services\BaseService;

class PromotionService extends BaseService
{
    public function __construct(PromotionRepository $repo)
    {
        parent::__construct($repo);
    }
} 