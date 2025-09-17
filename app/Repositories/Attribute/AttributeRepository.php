<?php

namespace App\Repositories\Attribute;

use App\Models\Attribute;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class AttributeRepository extends BaseRepository
{
    public function model(): string
    {
        return Attribute::class;
    }

    /**
     * Optimize relations for Attribute model
     */
    protected function optimizeRelations(array $relations): array
    {
        $optimizedRelations = [];
        foreach ($relations as $relation) {
            if (strpos($relation, ':') !== false) {
                // Nếu đã có select fields thì giữ nguyên
                $optimizedRelations[] = $relation;
            } else {
                // Tối ưu cho các relation của Attribute
                switch ($relation) {
                    case 'attributeValues':
                        $optimizedRelations[] = 'attributeValues:id,attribute_id,value';
                        break;
                    case 'values':
                        $optimizedRelations[] = 'values:id,attribute_id,value';
                        break;
                    default:
                        $optimizedRelations[] = $relation;
                }
            }
        }
        return $optimizedRelations;
    }

    /**
     * Get attributes with values
     */
    public function getAttributesWithValues(): array
    {
        return $this->model->with('attributeValues')->get()->toArray();
    }
} 