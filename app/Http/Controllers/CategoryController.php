<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * CategoryController: Xử lý các trang danh mục
 * - Hiển thị danh sách danh mục
 * - Hiển thị tin đăng theo danh mục
 */
class CategoryController extends Controller
{
  /**
   * Hiển thị danh sách tất cả danh mục
   */
  public function index()
  {
    // TODO: Load danh sách danh mục
    // $categories = Category::withCount('listings')
    //   ->orderBy('name')
    //   ->get();
    
    return view('pages.categories', [
      // 'categories' => $categories,
    ]);
  }

  /**
   * Hiển thị tin đăng theo danh mục
   * @param string $slug - Slug của danh mục
   */
  public function show($slug)
  {
    // TODO: Load danh mục và tin đăng
    // $category = Category::where('slug', $slug)->firstOrFail();
    // $listings = Listing::where('category_id', $category->id)
    //   ->where('status', 'approved')
    //   ->latest()
    //   ->paginate(20);
    
    return view('pages.category-listings', [
      // 'category' => $category,
      // 'listings' => $listings,
    ]);
  }
}


