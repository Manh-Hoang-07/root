<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo admin user
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'username' => 'admin',
            'password' => Hash::make('password'),
            'status' => UserStatus::Active,
            'created_user_id' => 1,
            'updated_user_id' => 1
        ]);

        // Tạo profile cho admin
        if (!$adminUser->profile) {
            $adminUser->profile()->create([
                'name' => 'Administrator',
                'gender' => 'male',
                'birthday' => '1990-01-01',
                'address' => 'Hà Nội, Việt Nam',
                'about' => 'Quản trị viên hệ thống',
                'created_user_id' => 1,
                'updated_user_id' => 1
            ]);
        }

        // Tạo user thường
        $normalUser = User::firstOrCreate([
            'email' => 'user@example.com'
        ], [
            'username' => 'user',
            'password' => Hash::make('password'),
            'status' => UserStatus::Active,
            'created_user_id' => 1,
            'updated_user_id' => 1
        ]);

        // Tạo profile cho user
        if (!$normalUser->profile) {
            $normalUser->profile()->create([
                'name' => 'User',
                'gender' => 'male',
                'birthday' => '1990-01-01',
                'address' => 'Việt Nam',
                'about' => 'Người dùng thường',
                'created_user_id' => 1,
                'updated_user_id' => 1
            ]);
        }

        $this->command->info('Auth seeder completed successfully!');
        $this->command->info('Admin credentials: admin@example.com / password');
        $this->command->info('User credentials: user@example.com / password');
    }
} 