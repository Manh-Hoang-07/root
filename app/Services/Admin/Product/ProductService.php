<?php

namespace App\Services\Admin\Product;

use App\Services\BaseService;
use App\Repositories\Product\ProductRepository;

class ProductService extends BaseService
{
    /**
     * @var ProductRepository
     */
    protected $repo;

    public function __construct(ProductRepository $repo)
    {
        parent::__construct($repo);
    }




    /**
     * Toggle featured status
     */
    public function toggleFeatured($id): array
    {
        try {
            $result = $this->repo->toggleFeatured($id);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Cập nhật trạng thái nổi bật thành công',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái nổi bật',
                'data' => null
            ];
        }
    }



    /**
     * Override create to ensure slug generation
     */
    public function create($data): array
    {
        $data = $this->ensureSlug($data);
        return parent::create($data);
    }

    /**
     * Override update to ensure slug generation
     */
    public function update($id, $data): array
    {
        $data = $this->ensureSlug($data);
        return parent::update($id, $data);
    }
}
