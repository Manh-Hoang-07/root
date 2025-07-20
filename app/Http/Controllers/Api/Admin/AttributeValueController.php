<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Services\AttributeValueService;
use App\Http\Resources\AttributeValueResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttributeValueController extends BaseController
{
    public function __construct(AttributeValueService $service)
    {
        parent::__construct($service, AttributeValueResource::class);
    }

    public function update(Request $request, $id)
    {
        Log::info('AttributeValueController::update called', [
            'id' => $id,
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);
        
        return parent::update($request, $id);
    }
} 