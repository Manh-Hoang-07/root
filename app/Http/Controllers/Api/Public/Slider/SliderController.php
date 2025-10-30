<?php

namespace App\Http\Controllers\Api\Public\Slider;

use App\Http\Controllers\Api\BaseController;
use App\Services\Public\Slider\SliderService;

class SliderController extends BaseController
{
    public function __construct(SliderService $service)
    {
        parent::__construct($service);
    }

    protected function getOptimizedData(array $filters, int $perPage, string $context = 'index', bool $single = false): array
    {
        $filters['visible_only'] = true;
        return parent::getOptimizedData($filters, $perPage, $context, $single);
    }
}
