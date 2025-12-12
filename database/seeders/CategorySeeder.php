<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * CategorySeeder: Tạo dữ liệu mẫu cho các danh mục bất động sản
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Đất thổ cư',
                'icon' => 'bi-house-door',
                'description' => 'Đất ở, đất thổ cư có thể xây nhà ở',
                'sort_order' => 1,
            ],
            [
                'name' => 'Đất nông nghiệp',
                'icon' => 'bi-tree',
                'description' => 'Đất trồng trọt, chăn nuôi, nuôi trồng thủy sản',
                'sort_order' => 2,
            ],
            [
                'name' => 'Đất dự án',
                'icon' => 'bi-building',
                'description' => 'Đất trong các dự án bất động sản',
                'sort_order' => 3,
            ],
            [
                'name' => 'Đất mặt tiền',
                'icon' => 'bi-shop',
                'description' => 'Đất có mặt tiền đường, phù hợp kinh doanh',
                'sort_order' => 4,
            ],
            [
                'name' => 'Đất biệt thự',
                'icon' => 'bi-house-heart',
                'description' => 'Đất xây biệt thự, nhà vườn',
                'sort_order' => 5,
            ],
            [
                'name' => 'Đất ven biển',
                'icon' => 'bi-water',
                'description' => 'Đất gần biển, nghỉ dưỡng',
                'sort_order' => 6,
            ],
            [
                'name' => 'Đất công nghiệp',
                'icon' => 'bi-factory',
                'description' => 'Đất xây nhà xưởng, khu công nghiệp',
                'sort_order' => 7,
            ],
            [
                'name' => 'Đất khác',
                'icon' => 'bi-grid',
                'description' => 'Các loại đất khác',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'slug' => Str::slug($category['name']),
                    'icon' => $category['icon'],
                    'description' => $category['description'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
