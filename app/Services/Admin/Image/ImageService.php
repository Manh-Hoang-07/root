<?php

namespace App\Services\Admin\Image;

use App\Repositories\Image\ImageRepository;
use App\Services\BaseService;

class ImageService extends BaseService
{
    public function __construct(ImageRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Get images by product
     */
    public function getImagesByProduct(int $productId): array
    {
        return $this->repo->getImagesByProduct($productId);
    }
}
