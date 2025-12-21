<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\City;
use App\Models\Category;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function index(Request $request)
    {
        $cities = City::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();

        // Load tin đăng với filter
        $query = Listing::active()
            ->with(['city', 'district', 'category', 'primaryImage', 'package']);

        // Lọc VIP
        if ($request->boolean('vip')) {
            $query->whereHas('package', fn ($q) => $q->where('code', 'vip'));
        }

        // Filter theo category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter theo city
        if ($request->has('city') && $request->city) {
            $query->where('city_id', $request->city);
        }

        // Filter theo giá (convert từ triệu đồng sang VND nếu cần)
        if ($request->has('max_price') && $request->max_price) {
            $maxPrice = $request->max_price;
            // If price is less than 10000, assume it's in millions and convert to VND
            $priceFilter = $maxPrice < 10000 ? $maxPrice * 1000000 : $maxPrice;
            $query->where('price', '<=', $priceFilter);
        }

        // Filter theo giá tối thiểu
        if ($request->has('min_price') && $request->min_price) {
            $minPrice = $request->min_price;
            $priceFilter = $minPrice < 10000 ? $minPrice * 1000000 : $minPrice;
            $query->where('price', '>=', $priceFilter);
        }

        // Filter theo diện tích
        if ($request->has('max_area') && $request->max_area) {
            $query->where('area', '<=', $request->max_area);
        }

        // Filter theo diện tích tối thiểu
        if ($request->has('min_area') && $request->min_area) {
            $query->where('area', '>=', $request->min_area);
        }

        // Filter đường ô tô
        if ($request->has('has_road') && $request->has_road) {
            $query->where('has_road_access', true);
        }

        // Filter theo tình trạng pháp lý
        if ($request->has('legal_status') && $request->legal_status) {
            $query->where('legal_status', $request->legal_status);
        }

        // Sort
        $sort = $request->get('sort');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'area_asc' => $query->orderBy('area', 'asc'),
            'area_desc' => $query->orderBy('area', 'desc'),
            'vip_first' => $query->orderByRaw("CASE WHEN package_id IS NOT NULL THEN 0 ELSE 1 END")->orderBy('created_at', 'desc'),
            default => $query->latest(),
        };

        // Lấy 50 tin đầu tiên để hiển thị trên map
        $listings = $query->take(50)->get();

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
        ]);
    }
}

