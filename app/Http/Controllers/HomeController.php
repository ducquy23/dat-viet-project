<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\City;
use App\Models\Category;
use App\Models\District;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function index(Request $request)
    {
        $cities = City::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();

        $districts = collect();
        if ($request->has('city')) {
            $districts = District::where('city_id', $request->city)
                ->active()
                ->ordered()
                ->get();
        }

        // Load tin đăng với filter
        $query = Listing::active()
            ->with(['city', 'district', 'category', 'primaryImage', 'package']);

        // Filter theo category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter theo city
        if ($request->has('city') && $request->city) {
            $query->where('city_id', $request->city);
        }

        // Filter theo district
        if ($request->has('district') && $request->district) {
            $query->where('district_id', $request->district);
        }

        // Filter theo giá (convert từ triệu đồng sang VND nếu cần)
        if ($request->has('max_price') && $request->max_price) {
            $maxPrice = $request->max_price;
            // If price is less than 10000, assume it's in millions and convert to VND
            $priceFilter = $maxPrice < 10000 ? $maxPrice * 1000000 : $maxPrice;
            $query->where('price', '<=', $priceFilter);
        }

        // Filter theo diện tích
        if ($request->has('max_area') && $request->max_area) {
            $query->where('area', '<=', $request->max_area);
        }

        // Filter đường ô tô
        if ($request->has('has_road') && $request->has_road) {
            $query->where('has_road_access', true);
        }

        // Lấy 50 tin đầu tiên để hiển thị trên map
        $listings = $query->latest()->take(50)->get();

        // Load VIP listings cho bottom bar
        $vipListings = Listing::active()
            ->whereHas('package', function ($q) {
                $q->where('code', 'vip');
            })
            ->with(['city', 'district', 'category', 'primaryImage', 'package'])
            ->latest()
            ->take(10)
            ->get();

        return view('pages.home', [
            'listings' => $listings,
            'vipListings' => $vipListings,
            'cities' => $cities,
            'categories' => $categories,
            'districts' => $districts,
        ]);
    }
}

