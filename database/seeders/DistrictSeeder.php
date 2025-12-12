<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * DistrictSeeder: Tạo dữ liệu mẫu cho các quận/huyện
 */
class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hồ Chí Minh
        $hcm = City::where('code', 'hcm')->first();
        if ($hcm) {
            $hcmDistricts = [
                ['name' => 'Quận 1', 'code' => 'q1'],
                ['name' => 'Quận 2', 'code' => 'q2'],
                ['name' => 'Quận 3', 'code' => 'q3'],
                ['name' => 'Quận 4', 'code' => 'q4'],
                ['name' => 'Quận 5', 'code' => 'q5'],
                ['name' => 'Quận 6', 'code' => 'q6'],
                ['name' => 'Quận 7', 'code' => 'q7'],
                ['name' => 'Quận 8', 'code' => 'q8'],
                ['name' => 'Quận 9', 'code' => 'q9'],
                ['name' => 'Quận 10', 'code' => 'q10'],
                ['name' => 'Quận 11', 'code' => 'q11'],
                ['name' => 'Quận 12', 'code' => 'q12'],
                ['name' => 'Quận Bình Tân', 'code' => 'qbt'],
                ['name' => 'Quận Bình Thạnh', 'code' => 'qbthanh'],
                ['name' => 'Quận Gò Vấp', 'code' => 'qgv'],
                ['name' => 'Quận Phú Nhuận', 'code' => 'qpn'],
                ['name' => 'Quận Tân Bình', 'code' => 'qtb'],
                ['name' => 'Quận Tân Phú', 'code' => 'qtp'],
                ['name' => 'Quận Thủ Đức', 'code' => 'qtd'],
                ['name' => 'Huyện Bình Chánh', 'code' => 'hbc'],
                ['name' => 'Huyện Cần Giờ', 'code' => 'hcg'],
                ['name' => 'Huyện Củ Chi', 'code' => 'hcc'],
                ['name' => 'Huyện Hóc Môn', 'code' => 'hhm'],
                ['name' => 'Huyện Nhà Bè', 'code' => 'hnb'],
            ];

            foreach ($hcmDistricts as $index => $district) {
                District::updateOrCreate(
                    ['code' => $district['code']],
                    [
                        'city_id' => $hcm->id,
                        'name' => $district['name'],
                        'slug' => Str::slug($hcm->code . '-' . $district['name']),
                        'code' => $district['code'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Hà Nội
        $hn = City::where('code', 'hn')->first();
        if ($hn) {
            $hnDistricts = [
                ['name' => 'Quận Ba Đình', 'code' => 'qbd'],
                ['name' => 'Quận Hoàn Kiếm', 'code' => 'qhk'],
                ['name' => 'Quận Tây Hồ', 'code' => 'qth'],
                ['name' => 'Quận Long Biên', 'code' => 'qlb'],
                ['name' => 'Quận Cầu Giấy', 'code' => 'qcg'],
                ['name' => 'Quận Đống Đa', 'code' => 'qdd'],
                ['name' => 'Quận Hai Bà Trưng', 'code' => 'qhbt'],
                ['name' => 'Quận Hoàng Mai', 'code' => 'qhm'],
                ['name' => 'Quận Thanh Xuân', 'code' => 'qtx'],
                ['name' => 'Huyện Sóc Sơn', 'code' => 'hss'],
                ['name' => 'Huyện Đông Anh', 'code' => 'hda'],
                ['name' => 'Huyện Gia Lâm', 'code' => 'hgl'],
                ['name' => 'Huyện Nam Từ Liêm', 'code' => 'hntl'],
                ['name' => 'Huyện Thanh Trì', 'code' => 'htt'],
                ['name' => 'Huyện Bắc Từ Liêm', 'code' => 'hbtl'],
                ['name' => 'Huyện Mê Linh', 'code' => 'hml'],
                ['name' => 'Huyện Hà Đông', 'code' => 'hhd'],
                ['name' => 'Huyện Sơn Tây', 'code' => 'hst'],
                ['name' => 'Huyện Ba Vì', 'code' => 'hbv'],
                ['name' => 'Huyện Phúc Thọ', 'code' => 'hpt'],
                ['name' => 'Huyện Đan Phượng', 'code' => 'hdp'],
                ['name' => 'Huyện Hoài Đức', 'code' => 'hhd2'],
                ['name' => 'Huyện Quốc Oai', 'code' => 'hqo'],
                ['name' => 'Huyện Thạch Thất', 'code' => 'htt2'],
                ['name' => 'Huyện Chương Mỹ', 'code' => 'hcm2'],
                ['name' => 'Huyện Thanh Oai', 'code' => 'hto'],
                ['name' => 'Huyện Thường Tín', 'code' => 'htt3'],
                ['name' => 'Huyện Phú Xuyên', 'code' => 'hpx'],
                ['name' => 'Huyện Ứng Hòa', 'code' => 'huh'],
                ['name' => 'Huyện Mỹ Đức', 'code' => 'hmd'],
            ];

            foreach ($hnDistricts as $index => $district) {
                District::updateOrCreate(
                    ['code' => $district['code']],
                    [
                        'city_id' => $hn->id,
                        'name' => $district['name'],
                        'slug' => Str::slug($hn->code . '-' . $district['name']),
                        'code' => $district['code'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Đà Nẵng
        $dn = City::where('code', 'dn')->first();
        if ($dn) {
            $dnDistricts = [
                ['name' => 'Quận Hải Châu', 'code' => 'qhc'],
                ['name' => 'Quận Thanh Khê', 'code' => 'qtk'],
                ['name' => 'Quận Sơn Trà', 'code' => 'qst'],
                ['name' => 'Quận Ngũ Hành Sơn', 'code' => 'qnhs'],
                ['name' => 'Quận Liên Chiểu', 'code' => 'qlc'],
                ['name' => 'Quận Cẩm Lệ', 'code' => 'qcl'],
                ['name' => 'Huyện Hòa Vang', 'code' => 'hhv'],
                ['name' => 'Huyện Hoàng Sa', 'code' => 'hhs'],
            ];

            foreach ($dnDistricts as $index => $district) {
                District::updateOrCreate(
                    ['code' => $district['code']],
                    [
                        'city_id' => $dn->id,
                        'name' => $district['name'],
                        'slug' => Str::slug($dn->code . '-' . $district['name']),
                        'code' => $district['code'],
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
