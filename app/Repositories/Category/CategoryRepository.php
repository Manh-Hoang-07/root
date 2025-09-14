<?php
namespace App\Repositories\Category;

use App\Models\Category;
use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    public function model()
    {
        return Category::class;
    }

    /**
     * Optimize relations for Category model
     */
    protected function optimizeRelations(array $relations): array
    {
        $optimizedRelations = [];
        foreach ($relations as $relation) {
            if (strpos($relation, ':') !== false) {
                // Nếu đã có select fields thì giữ nguyên
                $optimizedRelations[] = $relation;
            } else {
                // Tối ưu cho các relation của Category
                switch ($relation) {
                    case 'parent':
                        $optimizedRelations[] = 'parent:id,name';
                        break;
                    case 'children':
                        $optimizedRelations[] = 'children:id,name,parent_id';
                        break;
                    case 'products':
                        $optimizedRelations[] = 'products:id,name,slug';
                        break;
                    default:
                        $optimizedRelations[] = $relation;
                }
            }
        }
        return $optimizedRelations;
    }

    /**
     * Override searchable fields for Category
     */
    protected function getSearchableFields(): array
    {
        return ['name', 'description'];
    }

    /**
     * Override applyRelationshipSearch for Category
     */
    protected function applyRelationshipSearch(\Illuminate\Database\Eloquent\Builder $query, string $searchValue): void
    {
        // Search in parent category name
        $query->orWhereHas('parent', function($q) use ($searchValue) {
            $q->where('name', 'like', "%$searchValue%");
        });
    }
} 