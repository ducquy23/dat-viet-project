<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Listing;
use App\Models\Ad;
use Illuminate\Http\Request;

/**
 * ApiController: Xử lý các API endpoints cho AJAX requests
 * - Load quận/huyện theo thành phố
 * - Xử lý click quảng cáo
 * - Các API khác cho frontend
 */
class ApiController extends Controller
{
  /**
   * Lấy danh sách quận/huyện theo thành phố
   * @param Request $request - Chứa city_id
   */
  public function getDistricts(Request $request)
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
   * Xử lý click quảng cáo
   * @param int $id - ID của quảng cáo
   */
  public function trackAdClick($id)
  {
    $ad = Ad::find($id);
    if ($ad) {
      $ad->increment('clicks_count');
    }
    
    return response()->json(['success' => true]);
  }

  /**
   * Lấy danh sách tin đăng cho map (AJAX)
   * @param Request $request - Chứa các filter parameters
   */
  public function getListingsForMap(Request $request)
  {
    // Lấy các tham số filter
    $bounds = $request->get('bounds'); // [north, east, south, west]
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
   * Lấy chi tiết một tin đăng (AJAX)
   * @param int $id - ID của tin đăng
   */
  public function getListing($id)
  {
    $listing = Listing::active()
      ->with(['user', 'category', 'city', 'district', 'package', 'images' => function($query) {
        $query->orderBy('is_primary', 'desc')->orderBy('sort_order');
      }])
      ->findOrFail($id);
    
    return response()->json([
      'listing' => $listing,
    ]);
  }
}
