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
            'status' => UserStatus::Active
        ]);

        // Tạo admin user
        $adminUser = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'username' => 'admin',
            'password' => Hash::make('password'),
            'status' => UserStatus::Active
        ]);

        // Tạo profile cho admin
        if (!$adminUser->profile) {
            $adminUser->profile()->create([
                'name' => 'Administrator',
                'gender' => 'male',
                'birthday' => '1990-01-01',
                'address' => 'Hà Nội, Việt Nam',
                'about' => 'Quản trị viên hệ thống'
            ]);
        }

        // Tạo user thường
        $normalUser = User::firstOrCreate([
            'email' => 'user@example.com'
        ], [
            'username' => 'user',
            'password' => Hash::make('password'),
            'status' => UserStatus::Active
        ]);

        // Tạo thêm một số user mẫu
        $sampleUsers = [
            [
                'username' => 'john_doe',
                'email' => 'john@example.com',
                'name' => 'John Doe',
                'password' => 'password'
            ],
            [
                'username' => 'jane_smith',
                'email' => 'jane@example.com',
                'name' => 'Jane Smith',
                'password' => 'password'
            ],
            [
                'username' => 'bob_wilson',
                'email' => 'bob@example.com',
                'name' => 'Bob Wilson',
                'password' => 'password'
            ]
        ];

        foreach ($sampleUsers as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email']
            ], [
                'username' => $userData['username'],
                'password' => Hash::make($userData['password']),
                'status' => UserStatus::Active
            ]);

            if (!$user->profile) {
                $user->profile()->create([
                    'name' => $userData['name'],
                    'gender' => 'male',
                    'birthday' => '1990-01-01',
                    'address' => 'Việt Nam',
                    'about' => 'Người dùng mẫu'
                ]);
            }


        }

        $this->command->info('Auth seeder completed successfully!');
        $this->command->info('Admin credentials: admin@example.com / password');
        $this->command->info('User credentials: user@example.com / password');
    }
} 