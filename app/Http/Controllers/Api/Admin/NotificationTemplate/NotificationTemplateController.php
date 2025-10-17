<?php

namespace App\Http\Controllers\Api\Admin\NotificationTemplate;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Admin\NotificationTemplate\NotificationTemplateService;

class NotificationTemplateController extends CrudController
{
    /**
     * @var NotificationTemplateService
     */
    protected $service;

    public function __construct(NotificationTemplateService $service)
    {
        parent::__construct($service);
    }
}
