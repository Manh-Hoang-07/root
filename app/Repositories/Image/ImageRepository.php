<?php

namespace App\Repositories\Image;

use App\Models\Image;
use App\Repositories\BaseRepository;

class ImageRepository extends BaseRepository
{
    public function model(): string
    {
        return Image::class;
    }

    /**
     * Get images by product
     */
    public function getImagesByProduct(int $productId): array
    {
        return $this->model->where('imageable_type', 'App\\Models\\Product')
                          ->where('imageable_id', $productId)
                          ->orderBy('order', 'asc')
                          ->get()
                          ->toArray();
    }
} 