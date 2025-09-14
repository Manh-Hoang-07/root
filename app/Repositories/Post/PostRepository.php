<?php

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class PostRepository extends BaseRepository
{
    public function model(): string
    {
        return Post::class;
    }

    protected function applyOptimizedFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        parent::applyOptimizedFilters($query, $filters);

        if (!empty($filters['published_only'])) {
            $query->where('status', 'published')
                  ->whereNotNull('published_at')
                  ->where('published_at', '<=', Carbon::now());
        }

        if (!empty($filters['category_id'])) {
            $ids = is_array($filters['category_id']) ? $filters['category_id'] : explode(',', $filters['category_id']);
            $query->whereHas('categories', function ($q) use ($ids) {
                $q->whereIn('postcategory.id', $ids);
            });
        }

        if (!empty($filters['category_slug'])) {
            $slugs = is_array($filters['category_slug']) ? $filters['category_slug'] : explode(',', $filters['category_slug']);
            $query->whereHas('categories', function ($q) use ($slugs) {
                $q->whereIn('postcategory.slug', $slugs);
            });
        }

        if (!empty($filters['tag_id'])) {
            $ids = is_array($filters['tag_id']) ? $filters['tag_id'] : explode(',', $filters['tag_id']);
            $query->whereHas('tags', function ($q) use ($ids) {
                $q->whereIn('posttag.id', $ids);
            });
        }

        if (!empty($filters['tag_slug'])) {
            $slugs = is_array($filters['tag_slug']) ? $filters['tag_slug'] : explode(',', $filters['tag_slug']);
            $query->whereHas('tags', function ($q) use ($slugs) {
                $q->whereIn('posttag.slug', $slugs);
            });
        }
    }

    public function findBySlug(string $slug, array $relations = [], array $fields = ['*'])
    {
        $query = $this->getModel()->newQuery();

        if (!empty($relations)) {
            $query->with($this->optimizeRelations($relations));
        }
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($this->optimizeFields($fields));
        }

        return $query->where('slug', $slug)->first();
    }
}


