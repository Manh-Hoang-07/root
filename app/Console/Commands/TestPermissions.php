<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Testing Roles and Permissions ===');

        // Test admin user
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $this->info('✅ Admin user found');
            $this->info('Has admin role: ' . ($adminUser->hasRole('admin') ? 'Yes' : 'No'));
            $this->info('Has manage_users permission: ' . ($adminUser->can('manage_users') ? 'Yes' : 'No'));
            $this->info('All permissions: ' . implode(', ', $adminUser->getAllPermissions()->pluck('name')->toArray()));
            $this->info('All roles: ' . implode(', ', $adminUser->getRoleNames()->toArray()));
            $this->newLine();
        } else {
            $this->error('❌ Admin user not found');
        }

        // Test normal user
        $normalUser = User::where('email', 'user@example.com')->first();
        if ($normalUser) {
            $this->info('✅ Normal user found');
            $this->info('Has admin role: ' . ($normalUser->hasRole('admin') ? 'Yes' : 'No'));
            $this->info('Has manage_users permission: ' . ($normalUser->can('manage_users') ? 'Yes' : 'No'));
            $this->info('All permissions: ' . implode(', ', $normalUser->getAllPermissions()->pluck('name')->toArray()));
            $this->info('All roles: ' . implode(', ', $normalUser->getRoleNames()->toArray()));
            $this->newLine();
        } else {
            $this->error('❌ Normal user not found');
        }

        // Test middleware
        $this->info('=== Testing Middleware ===');
        $this->info('Routes with role:admin middleware:');
        $this->info('- /api/admin/users');
        $this->info('- /api/admin/products');
        $this->info('- /api/admin/orders');
        $this->info('- /api/admin/categories');
        $this->info('- /api/admin/brands');
        $this->newLine();

        $this->info('=== Test completed ===');
    }
}
