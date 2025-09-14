<?php
namespace App\Services\Admin\Attribute;

use App\Repositories\Attribute\AttributeValueRepository;
use App\Services\BaseService;
use Illuminate\Support\Str;

class AttributeValueService extends BaseService
{
    public function __construct(AttributeValueRepository $repo)
    {
        parent::__construct($repo);
    }
}