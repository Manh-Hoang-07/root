<?php

namespace App\Services\Public\Slider;

use App\Services\BaseService;
use App\Repositories\Slider\SliderRepository;

class SliderService extends BaseService
{
    public function __construct(SliderRepository $repo)
    {
        parent::__construct($repo);
    }
}
