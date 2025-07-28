<?php

namespace App\Services\Image;

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
    public function getImagesByProduct($productId)
    {
        return $this->repo->getImagesByProduct($productId);
    }
} 