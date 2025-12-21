<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Listing;
use App\Models\Ad;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ApiController extends Controller
{

    /**
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        $categories = Category::active()
            ->ordered()
            ->get(['id', 'name']);

        return response()->json([
            'categories' => $categories,
        ]);
    }


    /**
     * @return JsonResponse
     */
    public function getCities(): JsonResponse
    {
        $cities = City::active()
            ->ordered()
            ->get(['id', 'name']);

        return response()->json([
            'cities' => $cities,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDistricts(Request $request): JsonResponse
    {
        $cityId = $request->get('city_id');

        if (!$cityId) {
            return response()->json(['districts' => []]);
        }

        $districts = District::where('city_id', $cityId)
            ->active()
            ->ordered()
            ->get(['id', 'name']);

        return response()->json([
            'districts' => $districts,
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function trackAdClick($id): JsonResponse
    {
        // Track ad click (no longer storing clicks_count)
        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListingsForMap(Request $request): JsonResponse
    {
        // Enable query logging để xem SQL
        \DB::enableQueryLog();
        
        // Lấy các tham số filter
        $bounds = $request->get('bounds');
        $cityId = $request->get('city');
        $districtId = $request->get('district');
        $categoryId = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $minArea = $request->get('min_area');
        $maxArea = $request->get('max_area');
        $vip = $request->boolean('vip');
        $legalStatus = $request->get('legal_status');
        $sort = $request->get('sort');

        // Query listings trong bounds và filter
        $query = Listing::active()
            ->with(['city', 'district', 'category', 'primaryImage', 'package']);

        if ($bounds && is_array($bounds)) {
            $query->whereBetween('latitude', [$bounds['south'] ?? -90, $bounds['north'] ?? 90])
                ->whereBetween('longitude', [$bounds['west'] ?? -180, $bounds['east'] ?? 180]);
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

        if ($maxPrice) {
            // maxPrice can be in millions (from form) or VND (from API)
            // If it's less than 10000, assume it's in millions and convert
            $priceFilter = $maxPrice < 10000 ? $maxPrice * 1000000 : $maxPrice;
            $query->where('price', '<=', $priceFilter);
        }

        if ($minPrice) {
            $priceFilter = $minPrice < 10000 ? $minPrice * 1000000 : $minPrice;
            $query->where('price', '>=', $priceFilter);
        }

        if ($maxArea) {
            $query->where('area', '<=', $maxArea);
        }

        if ($minArea) {
            $query->where('area', '>=', $minArea);
        }

        if ($request->has('has_road') && $request->has_road) {
            $query->where('has_road_access', true);
        }

        if ($vip) {
            $query->whereHas('package', fn ($q) => $q->where('code', 'vip'));
        }

        if ($legalStatus) {
            $query->where('legal_status', $legalStatus);
        }

        $query = match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'area_asc' => $query->orderBy('area', 'asc'),
            'area_desc' => $query->orderBy('area', 'desc'),
            'vip_first' => $query->orderByRaw("CASE WHEN package_id IS NOT NULL THEN 0 ELSE 1 END")->orderBy('created_at', 'desc'),
            default => $query->latest(),
        };

        $listings = $query->take(100)->get()->map(function ($listing) {
            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'price' => $listing->price,
                'area' => $listing->area,
                'latitude' => $listing->latitude,
                'longitude' => $listing->longitude,
                'slug' => $listing->slug,
                'address' => $listing->address,
                'city' => $listing->city?->name,
                'district' => $listing->district?->name,
                'category' => $listing->category?->name,
                'image' => $listing->primaryImage?->image_path ?? null,
                'is_vip' => $listing->isVip(),
            ];
        });

        // Log SQL queries
        $queries = \DB::getQueryLog();
        \Log::info('SQL Queries for /api/listings/map', [
            'request_params' => $request->all(),
            'queries' => $queries,
            'query_count' => count($queries),
        ]);
        
        // Log từng câu SQL ra console/log
        foreach ($queries as $index => $queryLog) {
            \Log::info("SQL Query #" . ($index + 1), [
                'query' => $queryLog['query'],
                'bindings' => $queryLog['bindings'],
                'time' => $queryLog['time'] . 'ms',
            ]);
        }

        return response()->json([
            'listings' => $listings,
            '_debug' => [
                'sql_queries' => $queries,
                'query_count' => count($queries),
            ],
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getListing($id): JsonResponse
    {
        $listing = Listing::with(['user', 'category', 'city', 'district', 'package', 'images' => function ($query) {
                $query->orderBy('is_primary', 'desc')->orderBy('sort_order');
            }])
            ->findOrFail($id);

        $user = auth('partner')->user();
    
        if (!$listing->is_active && (!$user || $listing->user_id !== $user->id)) {
            abort(404);
        }

        return response()->json([
            'listing' => $listing,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getSearchSuggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }
        
        $suggestions = [];
        
        // Search in cities
        $cities = City::active()
            ->where('name', 'like', "%{$query}%")
            ->take(3)
            ->get(['id', 'name']);
        
        foreach ($cities as $city) {
            $suggestions[] = [
                'title' => $city->name,
                'subtitle' => 'Tỉnh/Thành phố',
                'type' => 'city',
                'id' => $city->id
            ];
        }
        
        // Search in districts
        $districts = District::active()
            ->where('name', 'like', "%{$query}%")
            ->with('city')
            ->take(3)
            ->get(['id', 'name', 'city_id']);
        
        foreach ($districts as $district) {
            $suggestions[] = [
                'title' => $district->name,
                'subtitle' => $district->city ? $district->city->name : 'Quận/Huyện',
                'type' => 'district',
                'id' => $district->id
            ];
        }
        
        // Search in categories
        $categories = Category::active()
            ->where('name', 'like', "%{$query}%")
            ->take(2)
            ->get(['id', 'name']);
        
        foreach ($categories as $category) {
            $suggestions[] = [
                'title' => $category->name,
                'subtitle' => 'Loại đất',
                'type' => 'category',
                'id' => $category->id
            ];
        }
        
        // Search in listings (titles and addresses)
        $listings = Listing::active()
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%");
            })
            ->with(['city', 'district'])
            ->take(3)
            ->get(['id', 'title', 'address', 'city_id', 'district_id']);
        
        foreach ($listings as $listing) {
            $location = [];
            if ($listing->district) $location[] = $listing->district->name;
            if ($listing->city) $location[] = $listing->city->name;
            
            $suggestions[] = [
                'title' => $listing->title,
                'subtitle' => implode(', ', $location),
                'type' => 'listing',
                'id' => $listing->id
            ];
        }
        
        return response()->json([
            'suggestions' => array_slice($suggestions, 0, 8)
        ]);
    }
}
