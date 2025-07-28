<?php

namespace App\Http\Controllers\Api\Admin\Image;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Image\ImageService;
use App\Http\Resources\Admin\Image\ImageResource;
use App\Http\Requests\Admin\Image\ImageRequest;

class ImageController extends BaseController
{
    public function __construct(ImageService $service)
    {
        parent::__construct($service, ImageResource::class);
        $this->storeRequestClass = ImageRequest::class;
        $this->updateRequestClass = ImageRequest::class;
    }

    /**
     * Get images for a product
     */
    public function getProductImages($productId)
    {
        $images = $this->service->getImagesByProduct($productId);
        return ImageResource::collection($images);
    }
} 