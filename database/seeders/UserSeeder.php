<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder: Tạo dữ liệu mẫu cho users (đối tác và admin)
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo admin
        User::updateOrCreate(
            ['email' => 'admin@datviet.com'],
            [
                'name' => 'Admin',
                'phone' => '0900000000',
                'email' => 'admin@datviet.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'phone_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // Tạo đối tác mẫu
        $partners = [
            [
                'name' => 'Nguyễn Văn A',
                'phone' => '0912345678',
                'email' => 'partner1@example.com',
            ],
            [
                'name' => 'Trần Thị B',
                'phone' => '0923456789',
                'email' => 'partner2@example.com',
            ],
            [
                'name' => 'Lê Văn C',
                'phone' => '0934567890',
                'email' => 'partner3@example.com',
            ],
            [
                'name' => 'Phạm Thị D',
                'phone' => '0945678901',
                'email' => 'partner4@example.com',
            ],
            [
                'name' => 'Hoàng Văn E',
                'phone' => '0956789012',
                'email' => 'partner5@example.com',
            ],
        ];

        foreach ($partners as $partner) {
            User::updateOrCreate(
                ['email' => $partner['email']],
                [
                    'name' => $partner['name'],
                    'phone' => $partner['phone'],
                    'email' => $partner['email'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'status' => 'active',
                    'phone_verified' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
