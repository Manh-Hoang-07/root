<?php

namespace App\Repositories\ConfigAudit;

use App\Repositories\BaseRepository;
use App\Models\ConfigAuditLog;

class ConfigAuditRepository extends BaseRepository
{
    /**
     * Get the model class name
     */
    public function model(): string
    {
        return ConfigAuditLog::class;
    }
}
