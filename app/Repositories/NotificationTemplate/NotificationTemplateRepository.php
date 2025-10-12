<?php

namespace App\Repositories\NotificationTemplate;

use App\Repositories\BaseRepository;
use App\Models\NotificationTemplate;

class NotificationTemplateRepository extends BaseRepository
{
    public function model(): string
    {
        return NotificationTemplate::class;
    }
}
