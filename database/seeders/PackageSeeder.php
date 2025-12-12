<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

/**
 * PackageSeeder: Tạo dữ liệu mẫu cho các gói đăng tin
 */
class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Gói Thường',
                'code' => 'normal',
                'description' => 'Gói đăng tin miễn phí, hiển thị cơ bản',
                'price' => 0,
                'duration_days' => 30,
                'priority' => 0,
                'features' => [
                    'pin_color' => 'blue',
                    'show_in_carousel' => false,
                    'priority_display' => false,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Gói VIP',
                'code' => 'vip',
                'description' => 'Gói đăng tin VIP, hiển thị nổi bật với pin màu vàng',
                'price' => 50000,
                'duration_days' => 30,
                'priority' => 10,
                'features' => [
                    'pin_color' => 'yellow',
                    'show_in_carousel' => true,
                    'priority_display' => true,
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Gói Premium',
                'code' => 'premium',
                'description' => 'Gói đăng tin cao cấp, hiển thị ưu tiên tối đa',
                'price' => 100000,
                'duration_days' => 60,
                'priority' => 20,
                'features' => [
                    'pin_color' => 'red',
                    'show_in_carousel' => true,
                    'priority_display' => true,
                    'top_position' => true,
                ],
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            Package::updateOrCreate(
                ['code' => $package['code']],
                $package
            );
        }
    }
}
