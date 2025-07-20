<?php
namespace App\Services\Category;

use App\Repositories\Category\CategoryRepository;
use App\Services\BaseService;

class CategoryService extends BaseService
{
    public function __construct(CategoryRepository $repo)
    {
        parent::__construct($repo);
    }
} 