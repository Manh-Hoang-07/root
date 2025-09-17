<?php

namespace App\Services\Public\PostCategory;

use App\Services\BaseService;
use App\Repositories\PostCategory\PostCategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class PostCategoryService extends BaseService
{
    public function __construct(PostCategoryRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Lấy danh sách danh mục công khai
     */
    public function getPublishedCategories(): array
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'slug', 'description'])
            ->toArray();
    }

    /**
     * Lấy danh mục theo slug
     */
    public function findBySlug(string $slug): ?array
    {
        $category = $this->repo->getModel()
            ->where('status', 'active')
            ->where('slug', $slug)
            ->first(['id', 'name', 'slug', 'description']);
        
        return $category ? $category->toArray() : null;
    }

    /**
     * Lấy danh mục có bài viết
     */
    public function getCategoriesWithPosts(): array
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->whereHas('posts', function($query) {
                $query->where('status', 'published');
            })
            ->withCount('posts')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'slug', 'description'])
            ->toArray();
    }

    /**
     * Lấy danh mục phổ biến (có nhiều bài viết nhất)
     */
    public function getPopularCategories($limit = 10): array
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->whereHas('posts', function($query) {
                $query->where('status', 'published');
            })
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'description'])
            ->toArray();
    }
}
