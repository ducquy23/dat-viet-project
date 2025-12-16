<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\City;
use App\Models\Category;
use App\Models\District;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        // Lấy các tham số tìm kiếm
        $keyword = $request->get('q', '');
        $cityId = $request->get('city_id');
        $districtId = $request->get('district_id');
        $categoryId = $request->get('category_id');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $minArea = $request->get('min_area');
        $maxArea = $request->get('max_area');
        $sort = $request->get('sort', 'latest'); // latest, price_asc, price_desc, area_asc, area_desc

        // Load cities và categories cho filter
        $cities = City::active()->ordered()->get();
        $categories = Category::active()->ordered()->get();
        $districts = collect();
        if ($cityId) {
            $districts = District::where('city_id', $cityId)->active()->ordered()->get();
        }

        // Thực hiện tìm kiếm trong database
        $query = Listing::active()
            ->with(['city', 'district', 'category', 'primaryImage', 'package']);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%")
                    ->orWhere('contact_name', 'like', "%{$keyword}%")
                    ->orWhere('legal_status', 'like', "%{$keyword}%")
                    ->orWhereHas('city', function ($cityQuery) use ($keyword) {
                        $cityQuery->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('district', function ($districtQuery) use ($keyword) {
                        $districtQuery->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($keyword) {
                        $categoryQuery->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        if ($districtId) {
            $query->where('district_id', $districtId);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($minPrice) {
            // Convert triệu to VND if needed
            $minPriceFilter = $minPrice < 10000 ? $minPrice * 1000000 : $minPrice;
            $query->where('price', '>=', $minPriceFilter);
        }

        if ($maxPrice) {
            // Convert triệu to VND if needed
            $maxPriceFilter = $maxPrice < 10000 ? $maxPrice * 1000000 : $maxPrice;
            $query->where('price', '<=', $maxPriceFilter);
        }

        if ($minArea) {
            $query->where('area', '>=', $minArea);
        }

        if ($maxArea) {
            $query->where('area', '<=', $maxArea);
        }

        // Sắp xếp
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'area_asc':
                $query->orderBy('area', 'asc');
                break;
            case 'area_desc':
                $query->orderBy('area', 'desc');
                break;
            default:
                $query->latest();
        }

        $listings = $query->paginate(12)->withQueryString();

        return view('pages.search', [
            'listings' => $listings,
            'cities' => $cities,
            'categories' => $categories,
            'districts' => $districts,
            'keyword' => $keyword,
            'filters' => [
                'city_id' => $cityId,
                'district_id' => $districtId,
                'category_id' => $categoryId,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'min_area' => $minArea,
                'max_area' => $maxArea,
                'sort' => $sort,
            ],
        ]);
    }
}

