<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Cities
        $cities = [
            ['name' => 'TP. Hồ Chí Minh', 'code' => 'hcm', 'slug' => 'tp-ho-chi-minh', 'sort_order' => 1],
            ['name' => 'Hà Nội', 'code' => 'hn', 'slug' => 'ha-noi', 'sort_order' => 2],
            ['name' => 'Đà Nẵng', 'code' => 'dn', 'slug' => 'da-nang', 'sort_order' => 3],
        ];
        DB::table('cities')->insert($cities);

        // Seed Districts for HCM
        $hcmId = DB::table('cities')->where('code', 'hcm')->first()->id;
        $districts = [
            ['city_id' => $hcmId, 'name' => 'Quận Phú Nhuận', 'code' => 'phunhuan', 'slug' => 'quan-phu-nhuan', 'sort_order' => 1],
            ['city_id' => $hcmId, 'name' => 'Quận Bình Thạnh', 'code' => 'binhthanh', 'slug' => 'quan-binh-thanh', 'sort_order' => 2],
            ['city_id' => $hcmId, 'name' => 'Quận 1', 'code' => 'quan1', 'slug' => 'quan-1', 'sort_order' => 3],
        ];
        DB::table('districts')->insert($districts);

        // Seed Categories
        $categories = [
            ['name' => 'Đất thổ cư', 'slug' => 'dat-tho-cu', 'icon' => 'bi-house-door', 'sort_order' => 1],
            ['name' => 'Đất nông nghiệp', 'slug' => 'dat-nong-nghiep', 'icon' => 'bi-tree', 'sort_order' => 2],
            ['name' => 'Đất thương mại', 'slug' => 'dat-thuong-mai', 'icon' => 'bi-shop', 'sort_order' => 3],
        ];
        DB::table('categories')->insert($categories);

        // Seed Packages
        $packages = [
            [
                'name' => 'Gói Thường',
                'code' => 'normal',
                'description' => 'Hiển thị cơ bản, pin màu xanh',
                'price' => 0,
                'duration_days' => 30,
                'priority' => 0,
                'features' => json_encode([
                    'pin_color' => 'blue',
                    'show_in_carousel' => false,
                    'highlight' => false,
                ]),
            ],
            [
                'name' => 'Gói VIP',
                'code' => 'vip',
                'description' => 'Pin màu vàng nổi bật, ưu tiên hiển thị, hiển thị trong carousel',
                'price' => 50000,
                'duration_days' => 30,
                'priority' => 100,
                'features' => json_encode([
                    'pin_color' => 'yellow',
                    'show_in_carousel' => true,
                    'highlight' => true,
                ]),
            ],
        ];
        DB::table('packages')->insert($packages);

        // Seed Admin User
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@datviet.com',
            'phone' => '0900000000',
            'password' => Hash::make('password'),
            'phone_verified' => true,
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed Sample Ads
        $ads = [
            [
                'title' => 'Dự án Bất động sản cao cấp',
                'description' => 'Giá ưu đãi đặc biệt',
                'image_url' => null,
                'link_url' => '#',
                'link_text' => 'Khám phá ngay',
                'position' => 'top',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Dịch vụ tư vấn BĐS',
                'description' => 'Chuyên nghiệp - Uy tín',
                'image_url' => null,
                'link_url' => '#',
                'link_text' => 'Liên hệ',
                'position' => 'sidebar_left',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Vay vốn mua đất',
                'description' => 'Lãi suất ưu đãi từ 0.8%/tháng',
                'image_url' => null,
                'link_url' => '#',
                'link_text' => 'Tìm hiểu',
                'position' => 'sidebar_right',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Ứng dụng Đất Việt',
                'description' => 'Tải ngay để nhận ưu đãi',
                'image_url' => null,
                'link_url' => '#',
                'link_text' => 'Tải về',
                'position' => 'bottom',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];
        DB::table('ads')->insert($ads);
    }
}

