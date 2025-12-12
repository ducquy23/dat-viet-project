<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * ListingController: Xử lý các trang liên quan đến tin đăng
 * - Hiển thị chi tiết tin đăng
 * - Hiển thị danh sách tin đăng theo danh mục
 * - Xử lý yêu thích, liên hệ
 */
class ListingController extends Controller
{
  /**
   * Hiển thị chi tiết tin đăng
   * @param string $slug - Slug của tin đăng
   */
  public function show($slug)
  {
    // TODO: Load tin đăng từ database
    // $listing = Listing::where('slug', $slug)
    //   ->where('status', 'approved')
    //   ->with(['user', 'category', 'city', 'district', 'images'])
    //   ->firstOrFail();
    
    // Tăng view count
    // $listing->increment('views_count');
    
    // Load tin liên quan
    // $relatedListings = Listing::where('category_id', $listing->category_id)
    //   ->where('id', '!=', $listing->id)
    //   ->where('status', 'approved')
    //   ->latest()
    //   ->take(6)
    //   ->get();
    
    return view('pages.listing-detail', [
      // 'listing' => $listing,
      // 'relatedListings' => $relatedListings,
    ]);
  }

  /**
   * Hiển thị danh sách tin đăng theo danh mục
   * @param string $categorySlug - Slug của danh mục
   */
  public function category($categorySlug)
  {
    // TODO: Load danh sách tin đăng theo danh mục
    // $category = Category::where('slug', $categorySlug)->firstOrFail();
    // $listings = Listing::where('category_id', $category->id)
    //   ->where('status', 'approved')
    //   ->latest()
    //   ->paginate(20);
    
    return view('pages.listings', [
      // 'category' => $category,
      // 'listings' => $listings,
    ]);
  }

  /**
   * Xử lý yêu thích tin đăng
   * @param Request $request
   * @param int $id - ID của tin đăng
   */
  public function toggleFavorite(Request $request, $id)
  {
    // TODO: Xử lý thêm/xóa yêu thích
    // if (!auth()->check()) {
    //   return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
    // }
    
    // $listing = Listing::findOrFail($id);
    // $user = auth()->user();
    
    // $favorite = Favorite::where('user_id', $user->id)
    //   ->where('listing_id', $id)
    //   ->first();
    
    // if ($favorite) {
    //   $favorite->delete();
    //   $listing->decrement('favorites_count');
    //   return response()->json(['favorited' => false]);
    // } else {
    //   Favorite::create([
    //     'user_id' => $user->id,
    //     'listing_id' => $id,
    //   ]);
    //   $listing->increment('favorites_count');
    //   return response()->json(['favorited' => true]);
    // }
    
    return response()->json(['message' => 'Chức năng đang phát triển']);
  }

  /**
   * Xử lý liên hệ với người đăng tin
   * @param Request $request
   * @param int $id - ID của tin đăng
   */
  public function contact(Request $request, $id)
  {
    // TODO: Xử lý gửi liên hệ
    // $request->validate([
    //   'name' => 'required|string|max:255',
    //   'phone' => 'required|string|max:20',
    //   'message' => 'nullable|string|max:1000',
    // ]);
    
    // $listing = Listing::findOrFail($id);
    
    // Contact::create([
    //   'listing_id' => $id,
    //   'name' => $request->name,
    //   'phone' => $request->phone,
    //   'message' => $request->message,
    // ]);
    
    // $listing->increment('contacts_count');
    
    return response()->json(['message' => 'Gửi liên hệ thành công']);
  }

  /**
   * Lưu tin đăng mới
   * @param Request $request
   */
  public function store(Request $request)
  {
    // TODO: Validate và lưu tin đăng
    // $request->validate([
    //   'latitude' => 'required|numeric',
    //   'longitude' => 'required|numeric',
    //   'price' => 'required|numeric|min:0',
    //   'area' => 'required|numeric|min:0',
    //   'contact_phone' => 'required|string|max:20',
    //   'images.*' => 'nullable|image|max:5120', // 5MB
    // ]);
    
    // $listing = Listing::create([
    //   'user_id' => auth()->id(),
    //   'latitude' => $request->latitude,
    //   'longitude' => $request->longitude,
    //   'price' => $request->price,
    //   'area' => $request->area,
    //   'contact_phone' => $request->contact_phone,
    //   'package_id' => $request->package_id ?? 1,
    //   // ... các trường khác
    // ]);
    
    // // Upload images
    // if ($request->hasFile('images')) {
    //   foreach ($request->file('images') as $image) {
    //     $path = $image->store('listings', 'public');
    //     ListingImage::create([
    //       'listing_id' => $listing->id,
    //       'image_path' => $path,
    //     ]);
    //   }
    // }
    
    return response()->json([
      'success' => true,
      'message' => 'Tin đăng đã được gửi và đang chờ duyệt',
    ]);
  }

  /**
   * Hiển thị danh sách tin đăng của user đã đăng nhập
   */
  public function myListings()
  {
    // TODO: Load tin đăng của user
    // $listings = Listing::where('user_id', auth()->id())
    //   ->latest()
    //   ->paginate(20);
    
    return view('pages.my-listings', [
      // 'listings' => $listings,
    ]);
  }
}

