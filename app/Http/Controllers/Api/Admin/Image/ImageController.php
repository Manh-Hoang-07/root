<?php

namespace App\Http\Controllers\Api\Admin\Image;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Image\ImageService;
use App\Http\Requests\Admin\Image\ImageRequest;
use Illuminate\Http\JsonResponse;

class ImageController extends BaseController
{
    public function __construct(ImageService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = ImageRequest::class;
        $this->updateRequestClass = ImageRequest::class;
    }

    /**
     * Get images for a product
     */
    public function getProductImages($productId): JsonResponse
    {
        $images = $this->service->getImagesByProduct($productId);
        return $this->successResponseWithFormat($images, 'Lấy danh sách thành công');
    }
} 