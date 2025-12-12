<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * SearchController: Xử lý tìm kiếm tin đăng
 * - Tìm kiếm theo từ khóa, địa điểm, giá, diện tích
 * - Filter và sắp xếp kết quả
 */
class SearchController extends Controller
{
  /**
   * Xử lý tìm kiếm tin đăng
   * @param Request $request - Chứa các tham số tìm kiếm
   */
  public function index(Request $request)
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
    
    // TODO: Thực hiện tìm kiếm trong database
    // $query = Listing::where('status', 'approved');
    
    // if ($keyword) {
    //   $query->where(function($q) use ($keyword) {
    //     $q->where('title', 'like', "%{$keyword}%")
    //       ->orWhere('description', 'like', "%{$keyword}%")
    //       ->orWhere('address', 'like', "%{$keyword}%");
    //   });
    // }
    
    // if ($cityId) {
    //   $query->where('city_id', $cityId);
    // }
    
    // if ($districtId) {
    //   $query->where('district_id', $districtId);
    // }
    
    // if ($categoryId) {
    //   $query->where('category_id', $categoryId);
    // }
    
    // if ($minPrice) {
    //   $query->where('price', '>=', $minPrice);
    // }
    
    // if ($maxPrice) {
    //   $query->where('price', '<=', $maxPrice);
    // }
    
    // if ($minArea) {
    //   $query->where('area', '>=', $minArea);
    // }
    
    // if ($maxArea) {
    //   $query->where('area', '<=', $maxArea);
    // }
    
    // // Sắp xếp
    // switch ($sort) {
    //   case 'price_asc':
    //     $query->orderBy('price', 'asc');
    //     break;
    //   case 'price_desc':
    //     $query->orderBy('price', 'desc');
    //     break;
    //   case 'area_asc':
    //     $query->orderBy('area', 'asc');
    //     break;
    //   case 'area_desc':
    //     $query->orderBy('area', 'desc');
    //     break;
    //   default:
    //     $query->latest();
    // }
    
    // $listings = $query->with(['city', 'district', 'category', 'images'])
    //   ->paginate(20)
    //   ->withQueryString();
    
    return view('pages.search', [
      // 'listings' => $listings,
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

