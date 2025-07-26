<?php
namespace App\Services\Attribute;

use App\Repositories\Attribute\AttributeValueRepository;
use App\Services\BaseService;
use Illuminate\Support\Str;

class AttributeValueService extends BaseService
{
    public function __construct(AttributeValueRepository $repo)
    {
        parent::__construct($repo);
    }

    public function create($data)
    {
        if (!empty($data['value'])) {
            $data['slug'] = Str::slug($data['value']);
        }
        return parent::create($data);
    }

    public function update($id, $data)
    {
        if (!empty($data['value'])) {
            $data['slug'] = Str::slug($data['value']);
        }
        return parent::update($id, $data);
    }
} 