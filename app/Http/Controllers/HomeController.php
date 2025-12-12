<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * HomeController: Xử lý trang chủ và các trang chính của site người dùng
 * - Hiển thị trang chủ với bản đồ và danh sách tin đăng
 * - Xử lý filter và tìm kiếm cơ bản
 */
class HomeController extends Controller
{
  /**
   * Hiển thị trang chủ
   * - Load danh sách tin đăng mới nhất
   * - Load các thành phố và quận/huyện
   * - Load các danh mục
   */
  public function index()
  {
    // TODO: Load dữ liệu từ database khi có models
    // $listings = Listing::where('status', 'approved')
    //   ->latest()
    //   ->take(20)
    //   ->get();
    
    // $cities = City::all();
    // $categories = Category::all();
    
    return view('pages.home', [
      // 'listings' => $listings,
      // 'cities' => $cities,
      // 'categories' => $categories,
    ]);
  }
}

