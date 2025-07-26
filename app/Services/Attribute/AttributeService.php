<?php
namespace App\Services\Attribute;

use App\Repositories\Attribute\AttributeRepository;
use App\Services\BaseService;
use Illuminate\Support\Str;

class AttributeService extends BaseService
{
    public function __construct(AttributeRepository $repo)
    {
        parent::__construct($repo);
    }
} 