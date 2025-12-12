<?php

namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Seeder;

/**
 * AdSeeder: Tạo dữ liệu mẫu cho quảng cáo
 */
class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ads = [
            [
                'title' => 'Dự án Bất động sản cao cấp',
                'description' => 'Khám phá các dự án bất động sản cao cấp với nhiều ưu đãi hấp dẫn',
                'image_url' => null,
                'link_url' => '#',
                'link_text' => 'Khám phá ngay',
                'position' => 'top',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Dịch vụ tư vấn BĐS',
                'description' => 'Tư vấn chuyên nghiệp về bất động sản, hỗ trợ tìm kiếm và đầu tư',
                'image_url' => null,
                'link_url' => '#',
                'link_text' => 'Liên hệ',
                'position' => 'sidebar_left',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Vay vốn mua đất',
                'description' => 'Hỗ trợ vay vốn với lãi suất ưu đãi, thủ tục nhanh chóng',
                'image_url' => null,
                'link_url' => '#',
                'link_text' => 'Tìm hiểu',
                'position' => 'sidebar_right',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Ứng dụng Đất Việt',
                'description' => 'Tải ứng dụng để tìm kiếm đất nhanh chóng và tiện lợi hơn',
                'image_url' => null,
                'link_url' => '#',
                'link_text' => 'Tải về',
                'position' => 'bottom',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($ads as $ad) {
            Ad::updateOrCreate(
                ['title' => $ad['title'], 'position' => $ad['position']],
                $ad
            );
        }
    }
}
