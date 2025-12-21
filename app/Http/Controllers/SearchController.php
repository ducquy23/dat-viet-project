<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\City;
use App\Models\Category;
use App\Models\District;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        // Support both 'city' and 'city_id' parameter names
        $cityId = $request->get('city') ?: $request->get('city_id');
        // Support both 'district' and 'district_id' parameter names
        $districtId = $request->get('district') ?: $request->get('district_id');
        // Support both 'category' and 'category_id' parameter names
        $categoryId = $request->get('category') ?: $request->get('category_id');
        // Get price filters - handle both direct values and empty strings
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        
        // Normalize max_price: empty string, null, or falsy values mean "unlimited"
        // Use input() instead of get() to get exact value including empty strings
        // Also check if it's the string "0" or numeric 0
        if ($maxPrice === '' || $maxPrice === null || $maxPrice === false || $maxPrice === '0' || $maxPrice === 0) {
            $maxPrice = null;
        }
        
        // Debug: Log price filter values (remove in production)
        \Log::info('Price filters debug', [
            'min_raw' => $request->input('min_price'),
            'max_raw' => $request->input('max_price'),
            'min' => $minPrice,
            'max' => $maxPrice,
            'max_type' => gettype($maxPrice),
            'all_params' => $request->all()
        ]);
        $minArea = $request->get('min_area');
        $maxArea = $request->get('max_area');
        $vip = $request->boolean('vip');
        $legalStatus = $request->get('legal_status');
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

        // Keyword search - only search in text fields, not in relationships if filters are set
        if ($keyword) {
            $query->where(function ($q) use ($keyword, $cityId, $districtId, $categoryId) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('address', 'like', "%{$keyword}%")
                    ->orWhere('contact_name', 'like', "%{$keyword}%")
                    ->orWhere('legal_status', 'like', "%{$keyword}%");
                
                // Only search in relationships if not already filtered by them
                if (!$cityId) {
                    $q->orWhereHas('city', function ($cityQuery) use ($keyword) {
                        $cityQuery->where('name', 'like', "%{$keyword}%");
                    });
                }
                
                if (!$districtId) {
                    $q->orWhereHas('district', function ($districtQuery) use ($keyword) {
                        $districtQuery->where('name', 'like', "%{$keyword}%");
                    });
                }
                
                if (!$categoryId) {
                    $q->orWhereHas('category', function ($categoryQuery) use ($keyword) {
                        $categoryQuery->where('name', 'like', "%{$keyword}%");
                    });
                }
            });
        }

        // Apply filters - these are AND conditions with keyword search
        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        if ($districtId) {
            $query->where('district_id', $districtId);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Handle price filters - values are already in VND (đồng) from hidden inputs
        // Handle min_price filter
        if ($minPrice && $minPrice !== '' && $minPrice !== null) {
            // minPrice is already in VND from hidden input (min_price_million * 1000000)
            // But check if it's accidentally in millions (less than 1000000)
            $minPriceFilter = (int)$minPrice;
            if ($minPriceFilter < 1000000) {
                // If less than 1 million, assume it's in millions and convert
                $minPriceFilter = $minPriceFilter * 1000000;
            }
            $query->where('price', '>=', $minPriceFilter);
        }

        // Handle max_price filter - only apply if value exists and is not empty/null
        // Empty string or null means "unlimited" - don't apply max price filter
        // After normalization above, $maxPrice is null if it was empty string/null/false
        if ($maxPrice !== null) {
            // maxPrice is already in VND from hidden input (max_price_million * 1000000)
            // But check if it's accidentally in millions (less than 1000000)
            $maxPriceFilter = (int)$maxPrice;
            if ($maxPriceFilter > 0) {
                if ($maxPriceFilter < 1000000) {
                    // If less than 1 million, assume it's in millions and convert
                    $maxPriceFilter = $maxPriceFilter * 1000000;
                }
                $query->where('price', '<=', $maxPriceFilter);
            }
        }
        // If maxPrice is null (was empty string/null/false), don't apply max price filter (unlimited)

        if ($minArea) {
            $query->where('area', '>=', $minArea);
        }

        if ($maxArea) {
            $query->where('area', '<=', $maxArea);
        }

        if ($vip) {
            $query->whereHas('package', fn ($q) => $q->where('code', 'vip'));
        }

        if ($legalStatus) {
            $query->where('legal_status', $legalStatus);
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
                'city' => $cityId,
                'city_id' => $cityId, // Keep for backward compatibility
                'district' => $districtId,
                'district_id' => $districtId, // Keep for backward compatibility
                'category' => $categoryId,
                'category_id' => $categoryId, // Keep for backward compatibility
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'min_area' => $minArea,
                'max_area' => $maxArea,
                'vip' => $vip,
                'legal_status' => $legalStatus,
                'sort' => $sort,
            ],
        ]);
    }
}

