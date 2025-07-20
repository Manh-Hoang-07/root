<?php
namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\BaseController;
use App\Services\Promotion\PromotionService;
use App\Http\Resources\PromotionResource;

class PromotionController extends BaseController
{
    public function __construct(PromotionService $service)
    {
        parent::__construct($service, PromotionResource::class);
    }
} 