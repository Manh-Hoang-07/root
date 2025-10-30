<?php

namespace App\Http\Controllers\Api\Admin\Slider;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Slider\SliderService;
use App\Http\Requests\Admin\Slider\SliderRequest;

class SliderController extends BaseController
{
    protected $storeRequestClass = SliderRequest::class;
    protected $updateRequestClass = SliderRequest::class;

    public function __construct(SliderService $service)
    {
        parent::__construct($service);
    }
}