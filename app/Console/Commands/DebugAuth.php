<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DebugAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug authentication and roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Debug Authentication ===');

        // Test 1: Kiểm tra user admin
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $this->info('✅ Admin user found');
            $this->info('ID: ' . $adminUser->id);
            $this->info('Email: ' . $adminUser->email);
            $this->info('Has admin role: ' . ($adminUser->hasRole('admin') ? 'Yes' : 'No'));
            $this->info('All roles: ' . implode(', ', $adminUser->getRoleNames()->toArray()));
            $this->info('All permissions: ' . implode(', ', $adminUser->getAllPermissions()->pluck('name')->toArray()));
            $this->newLine();
        } else {
            $this->error('❌ Admin user not found');
            $this->newLine();
        }

        // Test 2: Kiểm tra user thường
        $normalUser = User::where('email', 'user@example.com')->first();
        if ($normalUser) {
            $this->info('✅ Normal user found');
            $this->info('ID: ' . $normalUser->id);
            $this->info('Email: ' . $normalUser->email);
            $this->info('Has admin role: ' . ($normalUser->hasRole('admin') ? 'Yes' : 'No'));
            $this->info('All roles: ' . implode(', ', $normalUser->getRoleNames()->toArray()));
            $this->info('All permissions: ' . implode(', ', $normalUser->getAllPermissions()->pluck('name')->toArray()));
            $this->newLine();
        } else {
            $this->error('❌ Normal user not found');
            $this->newLine();
        }

        // Test 3: Kiểm tra tất cả users
        $this->info('=== All Users ===');
        $allUsers = User::all();
        foreach ($allUsers as $user) {
            $this->line("ID: {$user->id}, Email: {$user->email}, Roles: " . implode(', ', $user->getRoleNames()->toArray()));
        }

        $this->info('=== Debug completed ===');
    }
}
