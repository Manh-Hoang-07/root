<?php

namespace App\Services\Admin\NotificationTemplate;

use App\Services\BaseService;
use App\Repositories\NotificationTemplate\NotificationTemplateRepository;

class NotificationTemplateService extends BaseService
{
    /**
     * @var NotificationTemplateRepository
     */
    protected $repo;
    public function __construct(NotificationTemplateRepository $repo)
    {
        parent::__construct($repo);
    }
}
