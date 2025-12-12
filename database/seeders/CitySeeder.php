<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * CitySeeder: Tạo dữ liệu mẫu cho các tỉnh/thành phố
 */
class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'Hồ Chí Minh', 'code' => 'hcm', 'sort_order' => 1],
            ['name' => 'Hà Nội', 'code' => 'hn', 'sort_order' => 2],
            ['name' => 'Đà Nẵng', 'code' => 'dn', 'sort_order' => 3],
            ['name' => 'Cần Thơ', 'code' => 'ct', 'sort_order' => 4],
            ['name' => 'Hải Phòng', 'code' => 'hp', 'sort_order' => 5],
            ['name' => 'An Giang', 'code' => 'ag', 'sort_order' => 6],
            ['name' => 'Bà Rịa - Vũng Tàu', 'code' => 'brvt', 'sort_order' => 7],
            ['name' => 'Bắc Giang', 'code' => 'bg', 'sort_order' => 8],
            ['name' => 'Bắc Kạn', 'code' => 'bk', 'sort_order' => 9],
            ['name' => 'Bạc Liêu', 'code' => 'bl', 'sort_order' => 10],
            ['name' => 'Bắc Ninh', 'code' => 'bn', 'sort_order' => 11],
            ['name' => 'Bến Tre', 'code' => 'bt', 'sort_order' => 12],
            ['name' => 'Bình Định', 'code' => 'bd', 'sort_order' => 13],
            ['name' => 'Bình Dương', 'code' => 'bdu', 'sort_order' => 14],
            ['name' => 'Bình Phước', 'code' => 'bp', 'sort_order' => 15],
            ['name' => 'Bình Thuận', 'code' => 'bth', 'sort_order' => 16],
            ['name' => 'Cà Mau', 'code' => 'cm', 'sort_order' => 17],
            ['name' => 'Cao Bằng', 'code' => 'cb', 'sort_order' => 18],
            ['name' => 'Đắk Lắk', 'code' => 'dl', 'sort_order' => 19],
            ['name' => 'Đắk Nông', 'code' => 'dnong', 'sort_order' => 20],
            ['name' => 'Điện Biên', 'code' => 'db', 'sort_order' => 21],
            ['name' => 'Đồng Nai', 'code' => 'dna', 'sort_order' => 22],
            ['name' => 'Đồng Tháp', 'code' => 'dt', 'sort_order' => 23],
            ['name' => 'Gia Lai', 'code' => 'gl', 'sort_order' => 24],
            ['name' => 'Hà Giang', 'code' => 'hg', 'sort_order' => 25],
            ['name' => 'Hà Nam', 'code' => 'hnam', 'sort_order' => 26],
            ['name' => 'Hà Tĩnh', 'code' => 'ht', 'sort_order' => 27],
            ['name' => 'Hải Dương', 'code' => 'hd', 'sort_order' => 28],
            ['name' => 'Hậu Giang', 'code' => 'hgiang', 'sort_order' => 29],
            ['name' => 'Hòa Bình', 'code' => 'hb', 'sort_order' => 30],
            ['name' => 'Hưng Yên', 'code' => 'hy', 'sort_order' => 31],
            ['name' => 'Khánh Hòa', 'code' => 'kh', 'sort_order' => 32],
            ['name' => 'Kiên Giang', 'code' => 'kg', 'sort_order' => 33],
            ['name' => 'Kon Tum', 'code' => 'kt', 'sort_order' => 34],
            ['name' => 'Lai Châu', 'code' => 'lc', 'sort_order' => 35],
            ['name' => 'Lâm Đồng', 'code' => 'ld', 'sort_order' => 36],
            ['name' => 'Lạng Sơn', 'code' => 'ls', 'sort_order' => 37],
            ['name' => 'Lào Cai', 'code' => 'lca', 'sort_order' => 38],
            ['name' => 'Long An', 'code' => 'la', 'sort_order' => 39],
            ['name' => 'Nam Định', 'code' => 'nd', 'sort_order' => 40],
            ['name' => 'Nghệ An', 'code' => 'na', 'sort_order' => 41],
            ['name' => 'Ninh Bình', 'code' => 'nb', 'sort_order' => 42],
            ['name' => 'Ninh Thuận', 'code' => 'nt', 'sort_order' => 43],
            ['name' => 'Phú Thọ', 'code' => 'pt', 'sort_order' => 44],
            ['name' => 'Phú Yên', 'code' => 'py', 'sort_order' => 45],
            ['name' => 'Quảng Bình', 'code' => 'qb', 'sort_order' => 46],
            ['name' => 'Quảng Nam', 'code' => 'qnam', 'sort_order' => 47],
            ['name' => 'Quảng Ngãi', 'code' => 'qng', 'sort_order' => 48],
            ['name' => 'Quảng Ninh', 'code' => 'qn', 'sort_order' => 49],
            ['name' => 'Quảng Trị', 'code' => 'qt', 'sort_order' => 50],
            ['name' => 'Sóc Trăng', 'code' => 'st', 'sort_order' => 51],
            ['name' => 'Sơn La', 'code' => 'sl', 'sort_order' => 52],
            ['name' => 'Tây Ninh', 'code' => 'tn', 'sort_order' => 53],
            ['name' => 'Thái Bình', 'code' => 'tb', 'sort_order' => 54],
            ['name' => 'Thái Nguyên', 'code' => 'tng', 'sort_order' => 55],
            ['name' => 'Thanh Hóa', 'code' => 'th', 'sort_order' => 56],
            ['name' => 'Thừa Thiên Huế', 'code' => 'tth', 'sort_order' => 57],
            ['name' => 'Tiền Giang', 'code' => 'tg', 'sort_order' => 58],
            ['name' => 'Trà Vinh', 'code' => 'tv', 'sort_order' => 59],
            ['name' => 'Tuyên Quang', 'code' => 'tq', 'sort_order' => 60],
            ['name' => 'Vĩnh Long', 'code' => 'vl', 'sort_order' => 61],
            ['name' => 'Vĩnh Phúc', 'code' => 'vp', 'sort_order' => 62],
            ['name' => 'Yên Bái', 'code' => 'yb', 'sort_order' => 63],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['code' => $city['code']],
                [
                    'name' => $city['name'],
                    'slug' => Str::slug($city['name']),
                    'code' => $city['code'],
                    'sort_order' => $city['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
