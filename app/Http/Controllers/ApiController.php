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
        $ad = Ad::find($id);
        if ($ad) {
            $ad->increment('clicks_count');
        }

        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListingsForMap(Request $request): JsonResponse
    {
        // Lấy các tham số filter
        $bounds = $request->get('bounds');
        $cityId = $request->get('city');
        $districtId = $request->get('district');
        $categoryId = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $minArea = $request->get('min_area');
        $maxArea = $request->get('max_area');

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
            $query->where('price', '<=', $maxPrice);
        }

        if ($maxArea) {
            $query->where('area', '<=', $maxArea);
        }

        if ($request->has('has_road') && $request->has_road) {
            $query->where('has_road_access', true);
        }

        $listings = $query->latest()->take(100)->get()->map(function ($listing) {
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

        return response()->json([
            'listings' => $listings,
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getListing($id): JsonResponse
    {
        $listing = Listing::active()
            ->with(['user', 'category', 'city', 'district', 'package', 'images' => function ($query) {
                $query->orderBy('is_primary', 'desc')->orderBy('sort_order');
            }])
            ->findOrFail($id);

        return response()->json([
            'listing' => $listing,
        ]);
    }
}
