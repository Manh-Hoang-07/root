<?php
namespace App\Repositories\Brand;

use App\Models\Brand;
use App\Repositories\BaseRepository;

class BrandRepository extends BaseRepository
{
    public function model(): string
    {
        return Brand::class;
    }

    /**
     * Optimize relations for Brand model
     */
    protected function optimizeRelations(array $relations): array
    {
        $optimizedRelations = [];
        foreach ($relations as $relation) {
            if (strpos($relation, ':') !== false) {
                // Nếu đã có select fields thì giữ nguyên
                $optimizedRelations[] = $relation;
            } else {
                // Tối ưu cho các relation của Brand
                switch ($relation) {
                    case 'products':
                        $optimizedRelations[] = 'products:id,name,slug,price,image';
                        break;
                    default:
                        $optimizedRelations[] = $relation;
                }
            }
        }
        return $optimizedRelations;
    }
} 