<?php
namespace App\Services\Admin\Promotion;

use App\Repositories\Promotion\PromotionRepository;
use App\Services\BaseService;

class PromotionService extends BaseService
{
    /**
     * @var PromotionRepository
     */
    protected $repo;

    public function __construct(PromotionRepository $repo)
    {
        parent::__construct($repo);
    }
}