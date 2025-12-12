<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Hà Nội', 'type' => 'Thành phố', 'code' => '101'],
            ['name' => 'Bắc Ninh', 'type' => 'Tỉnh', 'code' => '223'],
            ['name' => 'Quảng Ninh', 'type' => 'Tỉnh', 'code' => '225'],
            ['name' => 'Hải Phòng', 'type' => 'Thành phố', 'code' => '103'],
            ['name' => 'Hưng Yên', 'type' => 'Tỉnh', 'code' => '109'],
            ['name' => 'Ninh Bình', 'type' => 'Tỉnh', 'code' => '117'],
            ['name' => 'Cao Bằng', 'type' => 'Tỉnh', 'code' => '203'],
            ['name' => 'Tuyên Quang', 'type' => 'Tỉnh', 'code' => '211'],
            ['name' => 'Lào Cai', 'type' => 'Tỉnh', 'code' => '205'],
            ['name' => 'Thái Nguyên', 'type' => 'Tỉnh', 'code' => '215'],
            ['name' => 'Lạng Sơn', 'type' => 'Tỉnh', 'code' => '209'],
            ['name' => 'Phú Thọ', 'type' => 'Tỉnh', 'code' => '217'],
            ['name' => 'Điện Biên', 'type' => 'Tỉnh', 'code' => '301'],
            ['name' => 'Lai Châu', 'type' => 'Tỉnh', 'code' => '302'],
            ['name' => 'Sơn La', 'type' => 'Tỉnh', 'code' => '303'],
            ['name' => 'Thanh Hoá', 'type' => 'Tỉnh', 'code' => '401'],
            ['name' => 'Nghệ An', 'type' => 'Tỉnh', 'code' => '403'],
            ['name' => 'Hà Tĩnh', 'type' => 'Tỉnh', 'code' => '405'],
            ['name' => 'Quảng Trị', 'type' => 'Tỉnh', 'code' => '409'],
            ['name' => 'Huế', 'type' => 'Thành phố', 'code' => '411'],
            ['name' => 'Đà Nẵng', 'type' => 'Thành phố', 'code' => '501'],
            ['name' => 'Quảng Ngãi', 'type' => 'Tỉnh', 'code' => '505'],
            ['name' => 'Khánh Hòa', 'type' => 'Tỉnh', 'code' => '511'],
            ['name' => 'Gia Lai', 'type' => 'Tỉnh', 'code' => '603'],
            ['name' => 'Đắk Lắk', 'type' => 'Tỉnh', 'code' => '605'],
            ['name' => 'Lâm Đồng', 'type' => 'Tỉnh', 'code' => '703'],
            ['name' => 'Tây Ninh', 'type' => 'Tỉnh', 'code' => '709'],
            ['name' => 'Đồng Nai', 'type' => 'Tỉnh', 'code' => '713'],
            ['name' => 'Hồ Chí Minh', 'type' => 'Thành phố', 'code' => '701'],
            ['name' => 'Vĩnh Long', 'type' => 'Tỉnh', 'code' => '809'],
            ['name' => 'Đồng Tháp', 'type' => 'Tỉnh', 'code' => '803'],
            ['name' => 'An Giang', 'type' => 'Tỉnh', 'code' => '805'],
            ['name' => 'Cần Thơ', 'type' => 'Thành phố', 'code' => '815'],
            ['name' => 'Cà Mau', 'type' => 'Tỉnh', 'code' => '823'],
        ];

        foreach ($cities as $index => $city) {
            City::updateOrCreate(
                ['code' => $city['code']],
                [
                    'name' => $city['name'],
                    'type' => $city['type'],
                    'slug' => Str::slug($city['name']),
                    'is_active' => true,
                ]
            );
        }
    }
}

