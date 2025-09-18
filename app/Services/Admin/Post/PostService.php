<?php

namespace App\Services\Admin\Post;

use App\Services\BaseService;
use App\Repositories\Post\PostRepository;

class PostService extends BaseService
{
    /**
     * @var PostRepository
     */
    protected $repo;

    public function __construct(PostRepository $repo)
    {
        parent::__construct($repo);
    }

    public function create($data): array
    {
        $data = $this->ensureSlug($data);
        $tagIds = $data['tag_ids'] ?? null;
        $categoryIds = $data['category_ids'] ?? null;
        unset($data['tag_ids'], $data['category_ids']);
        $post = parent::create($data);
        if ($post && is_array($tagIds)) {
            $postModel = $this->repo->getModel()->find($post['id']);
            $postModel->tags()->sync($tagIds);
        }
        if ($post && is_array($categoryIds)) {
            $postModel = $this->repo->getModel()->find($post['id']);
            $postModel->categories()->sync($categoryIds);
        }
        return $post;
    }

    public function update($id, $data): ?array
    {
        $data = $this->ensureSlug($data);
        $tagIds = $data['tag_ids'] ?? null;
        $categoryIds = $data['category_ids'] ?? null;
        unset($data['tag_ids'], $data['category_ids']);
        $post = parent::update($id, $data);
        if (!$post) return null;
        if (is_array($tagIds)) {
            $postModel = $this->repo->getModel()->find($post['id']);
            $postModel->tags()->sync($tagIds);
        }
        if (is_array($categoryIds)) {
            $postModel = $this->repo->getModel()->find($post['id']);
            $postModel->categories()->sync($categoryIds);
        }
        return $post;
    }

    // Deprecated: use BaseService::findOneBy(['slug' => $slug], ...)
}


