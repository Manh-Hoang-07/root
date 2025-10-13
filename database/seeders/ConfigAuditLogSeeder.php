<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfigAuditLog;

class ConfigAuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $auditLogs = [
            [
                'config_key' => 'app.name',
                'old_value' => 'Laravel',
                'new_value' => 'My Application',
                'action' => 'updated',
                'change_reason' => 'Initial setup',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => ['source' => 'seeder'],
                'created_user_id' => 1,
                'updated_user_id' => 1
            ],
            [
                'config_key' => 'mail.default',
                'old_value' => 'smtp',
                'new_value' => 'smtp',
                'action' => 'created',
                'change_reason' => 'Email configuration setup',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => ['source' => 'seeder'],
                'created_user_id' => 1,
                'updated_user_id' => 1
            ],
            [
                'config_key' => 'cache.default',
                'old_value' => null,
                'new_value' => 'file',
                'action' => 'created',
                'change_reason' => 'Cache configuration setup',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => ['source' => 'seeder'],
                'created_user_id' => 1,
                'updated_user_id' => 1
            ]
        ];

        foreach ($auditLogs as $logData) {
            ConfigAuditLog::create($logData);
        }

        $this->command->info('Config audit logs seeded successfully!');
    }
}
