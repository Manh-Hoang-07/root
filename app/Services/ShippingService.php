<?php
namespace App\Services;

use App\Repositories\ShippingServiceRepository;

class ShippingService extends BaseService
{
    public function __construct(ShippingServiceRepository $repo)
    {
        parent::__construct($repo);
    }

    public function toggleStatus($id, $status)
    {
        $service = $this->repo->find($id);
        $service->status = $status;
        $service->save();
        return $service;
    }
} 