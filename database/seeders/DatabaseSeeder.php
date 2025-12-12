<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder: Seeder chính, gọi tất cả các seeders khác theo thứ tự
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Thứ tự seed quan trọng:
     * 1. City (tỉnh/thành phố) - không phụ thuộc
     * 2. District (quận/huyện) - phụ thuộc City
     * 3. Category (danh mục) - không phụ thuộc
     * 4. Package (gói đăng tin) - không phụ thuộc
     * 5. User (người dùng) - không phụ thuộc
     * 6. Listing (tin đăng) - phụ thuộc User, Category, City, District, Package
     * 7. Ad (quảng cáo) - không phụ thuộc
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            PackageSeeder::class,
            UserSeeder::class,
            CitySeeder::class,
            ListingSeeder::class,
            AdSeeder::class,
        ]);
    }
}

