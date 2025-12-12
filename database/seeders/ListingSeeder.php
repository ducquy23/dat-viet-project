<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * ListingSeeder: Tạo dữ liệu mẫu cho tin đăng
 */
class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        // Lấy city theo tên (vì code đã đổi sang mã hành chính)
        $hcm = City::where('name', 'Hồ Chí Minh')->first();
        $hn  = City::where('name', 'Hà Nội')->first();
        $hcmDistricts = $hcm ? District::where('city_id', $hcm->id)->get() : collect();
        $hnDistricts  = $hn ? District::where('city_id', $hn->id)->get() : collect();
        $normalPackage = Package::where('code', 'normal')->first();
        $vipPackage = Package::where('code', 'vip')->first();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Cần chạy UserSeeder và CategorySeeder trước!');
            return;
        }

        $listings = [
            [
                'title' => 'Đất thổ cư 100m² mặt tiền đường Nguyễn Văn Cừ, Quận 5',
                'description' => 'Đất thổ cư đẹp, sổ đỏ riêng, mặt tiền 5m, đường ô tô vào tận nơi. Vị trí đẹp, gần chợ, trường học, bệnh viện. Phù hợp xây nhà ở hoặc kinh doanh.',
                'address' => '123 Nguyễn Văn Cừ, Phường 4, Quận 5, TP.HCM',
                'latitude' => 10.762622,
                'longitude' => 106.660172,
                'price' => 2500,
                'price_per_m2' => 25,
                'area' => 100,
                'front_width' => 5,
                'depth' => 20,
                'legal_status' => 'Sổ đỏ',
                'road_type' => 'Ô tô',
                'road_width' => 8,
                'direction' => 'Nam',
                'has_road_access' => true,
                'contact_name' => 'Nguyễn Văn A',
                'contact_phone' => '0912345678',
                'status' => 'approved',
                'city' => 'Hồ Chí Minh',
                'district' => 'Quận 5',
                'category' => 'Đất thổ cư',
            ],
            [
                'title' => 'Đất nông nghiệp 5000m² tại Củ Chi, có sổ hồng',
                'description' => 'Đất nông nghiệp rộng rãi, có sổ hồng, phù hợp trồng trọt, chăn nuôi. Đường đất rộng, gần khu dân cư.',
                'address' => 'Xã An Nhơn Tây, Huyện Củ Chi, TP.HCM',
                'latitude' => 10.973611,
                'longitude' => 106.493333,
                'price' => 5000,
                'price_per_m2' => 1,
                'area' => 5000,
                'legal_status' => 'Sổ hồng',
                'road_type' => 'Đường đất',
                'has_road_access' => true,
                'contact_name' => 'Trần Thị B',
                'contact_phone' => '0923456789',
                'status' => 'pending',
                'city' => 'Hồ Chí Minh',
                'district' => 'Huyện Củ Chi',
                'category' => 'Đất nông nghiệp',
            ],
            [
                'title' => 'Đất mặt tiền 80m² đường Lê Văn Việt, Quận 9',
                'description' => 'Đất mặt tiền đẹp, vị trí đắc địa, phù hợp kinh doanh. Sổ đỏ riêng, đường ô tô 12m.',
                'address' => '456 Lê Văn Việt, Phường Hiệp Phú, Quận 9, TP.HCM',
                'latitude' => 10.8422,
                'longitude' => 106.8097,
                'price' => 8000,
                'price_per_m2' => 100,
                'area' => 80,
                'front_width' => 4,
                'depth' => 20,
                'legal_status' => 'Sổ đỏ',
                'road_type' => 'Ô tô',
                'road_width' => 12,
                'direction' => 'Đông',
                'has_road_access' => true,
                'contact_name' => 'Lê Văn C',
                'contact_phone' => '0934567890',
                'status' => 'approved',
                'city' => 'Hồ Chí Minh',
                'district' => 'Quận 9',
                'category' => 'Đất mặt tiền',
                'package' => 'vip',
            ],
            [
                'title' => 'Đất thổ cư 150m² tại Quận 2, gần sông',
                'description' => 'Đất thổ cư đẹp, gần sông, không khí trong lành. Sổ đỏ riêng, đường ô tô vào tận nơi.',
                'address' => '789 Đường số 1, Phường An Phú, Quận 2, TP.HCM',
                'latitude' => 10.7870,
                'longitude' => 106.7493,
                'price' => 4500,
                'price_per_m2' => 30,
                'area' => 150,
                'front_width' => 6,
                'depth' => 25,
                'legal_status' => 'Sổ đỏ',
                'road_type' => 'Ô tô',
                'road_width' => 10,
                'direction' => 'Đông Nam',
                'has_road_access' => true,
                'contact_name' => 'Phạm Thị D',
                'contact_phone' => '0945678901',
                'status' => 'approved',
                'city' => 'Hồ Chí Minh',
                'district' => 'Quận 2',
                'category' => 'Đất thổ cư',
            ],
            [
                'title' => 'Đất dự án 200m² tại Hà Nội, có sổ đỏ',
                'description' => 'Đất trong dự án, có sổ đỏ riêng, hạ tầng đồng bộ. Phù hợp xây nhà ở hoặc đầu tư.',
                'address' => '123 Đường Láng, Phường Láng Thượng, Quận Đống Đa, Hà Nội',
                'latitude' => 21.0285,
                'longitude' => 105.8048,
                'price' => 6000,
                'price_per_m2' => 30,
                'area' => 200,
                'front_width' => 8,
                'depth' => 25,
                'legal_status' => 'Sổ đỏ',
                'road_type' => 'Ô tô',
                'road_width' => 15,
                'direction' => 'Nam',
                'has_road_access' => true,
                'contact_name' => 'Hoàng Văn E',
                'contact_phone' => '0956789012',
                'status' => 'pending',
                'city' => 'Hà Nội',
                'district' => 'Quận Đống Đa',
                'category' => 'Đất dự án',
            ],
        ];

        foreach ($listings as $index => $listingData) {
            $user = $users->random();
            $category = $categories->firstWhere('name', $listingData['category']);
            $city = City::where('name', $listingData['city'])->first();
            $district = null;
            if (isset($listingData['district'])) {
                $target = $listingData['district'];
                if ($city && $city->id === $hcm?->id) {
                    $district = $hcmDistricts->firstWhere('name', $target);
                } elseif ($city && $city->id === $hn?->id) {
                    $district = $hnDistricts->firstWhere('name', $target);
                } else {
                    $district = District::where('city_id', $city?->id)->where('name', $target)->first();
                }
            }
            $package = isset($listingData['package']) && $listingData['package'] === 'vip'
                ? $vipPackage
                : $normalPackage;

            if (!$category || !$city || !$package) {
                continue;
            }

            $listing = Listing::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'city_id' => $city->id,
                'district_id' => $district?->id,
                'package_id' => $package->id,
                'title' => $listingData['title'],
                'description' => $listingData['description'],
                'address' => $listingData['address'],
                'latitude' => $listingData['latitude'],
                'longitude' => $listingData['longitude'],
                'price' => $listingData['price'],
                'price_per_m2' => $listingData['price_per_m2'] ?? null,
                'area' => $listingData['area'],
                'front_width' => $listingData['front_width'] ?? null,
                'depth' => $listingData['depth'] ?? null,
                'legal_status' => $listingData['legal_status'] ?? null,
                'road_type' => $listingData['road_type'] ?? null,
                'road_width' => $listingData['road_width'] ?? null,
                'direction' => $listingData['direction'] ?? null,
                'has_road_access' => $listingData['has_road_access'] ?? false,
                'contact_name' => $listingData['contact_name'],
                'contact_phone' => $listingData['contact_phone'],
                'status' => $listingData['status'],
                'approved_at' => $listingData['status'] === 'approved' ? now() : null,
                'expires_at' => $listingData['status'] === 'approved' ? now()->addDays(30) : null,
            ]);
        }
    }
}
