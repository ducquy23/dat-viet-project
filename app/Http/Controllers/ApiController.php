<?php

namespace App\Http\Controllers;

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
    
    // TODO: Load quận/huyện từ database
    // $districts = District::where('city_id', $cityId)
    //   ->orderBy('name')
    //   ->get(['id', 'name']);
    
    return response()->json([
      'districts' => [], // $districts
    ]);
  }

  /**
   * Xử lý click quảng cáo
   * @param int $id - ID của quảng cáo
   */
  public function trackAdClick($id)
  {
    // TODO: Tăng click count và lưu log
    // $ad = Ad::find($id);
    // if ($ad) {
    //   $ad->increment('clicks_count');
    //   // Có thể lưu log click với IP, user agent, etc.
    // }
    
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
    $cityId = $request->get('city_id');
    $districtId = $request->get('district_id');
    $categoryId = $request->get('category_id');
    $minPrice = $request->get('min_price');
    $maxPrice = $request->get('max_price');
    $minArea = $request->get('min_area');
    $maxArea = $request->get('max_area');
    
    // TODO: Query listings trong bounds và filter
    // $query = Listing::where('status', 'approved');
    
    // if ($bounds) {
    //   $query->whereBetween('latitude', [$bounds['south'], $bounds['north']])
    //     ->whereBetween('longitude', [$bounds['west'], $bounds['east']]);
    // }
    
    // // Apply filters...
    // $listings = $query->with(['city', 'district', 'category', 'images'])
    //   ->get(['id', 'title', 'price', 'area', 'latitude', 'longitude', 'slug']);
    
    return response()->json([
      'listings' => [],
    ]);
  }
}

