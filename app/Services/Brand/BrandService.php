<?php
namespace App\Services\Brand;

use App\Repositories\Brand\BrandRepository;
use App\Services\BaseService;

class BrandService extends BaseService
{
    public function __construct(BrandRepository $repo)
    {
        parent::__construct($repo);
    }
} 