<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
    $listing = Listing::where('slug', $slug)
      ->active()
      ->with(['user', 'category', 'city', 'district', 'package', 'images' => function($query) {
        $query->orderBy('is_primary', 'desc')->orderBy('sort_order');
      }])
      ->firstOrFail();
    
    // Tăng view count
    $listing->increment('views_count');
    
    // Ghi log view
    \App\Models\ListingView::create([
      'listing_id' => $listing->id,
      'user_id' => auth('partner')->id(), // null nếu guest
      'ip_address' => request()->ip(),
      'user_agent' => request()->userAgent(),
      'viewed_at' => now(),
    ]);
    
    // Kiểm tra user đã yêu thích chưa
    $isFavorited = false;
    $user = auth('partner')->user();
    if ($user) {
      $isFavorited = Favorite::where('user_id', $user->id)
        ->where('listing_id', $listing->id)
        ->exists();
    }
    
    // Load tin liên quan
    $relatedListings = Listing::where('category_id', $listing->category_id)
      ->where('id', '!=', $listing->id)
      ->active()
      ->with(['city', 'district', 'category', 'primaryImage'])
      ->latest()
      ->take(6)
      ->get();
    
    return view('pages.listing-detail', [
      'listing' => $listing,
      'relatedListings' => $relatedListings,
      'isFavorited' => $isFavorited,
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
    // Cho phép cả partner và guest (nếu cần)
    $user = auth('partner')->user();
    
    if (!$user) {
      return response()->json(['error' => 'Vui lòng đăng nhập'], 401);
    }
    
    $listing = Listing::findOrFail($id);
    
    $favorite = Favorite::where('user_id', $user->id)
      ->where('listing_id', $id)
      ->first();
    
    if ($favorite) {
      $favorite->delete();
      $listing->decrement('favorites_count');
      return response()->json(['favorited' => false, 'message' => 'Đã bỏ yêu thích']);
    } else {
      Favorite::create([
        'user_id' => $user->id,
        'listing_id' => $id,
      ]);
      $listing->increment('favorites_count');
      return response()->json(['favorited' => true, 'message' => 'Đã thêm vào yêu thích']);
    }
  }

  /**
   * Xử lý liên hệ với người đăng tin
   * @param Request $request
   * @param int $id - ID của tin đăng
   */
  public function contact(Request $request, $id)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'phone' => 'required|string|max:20',
      'message' => 'nullable|string|max:1000',
    ]);
    
    $listing = Listing::findOrFail($id);
    $userId = auth('partner')->id(); // Có thể null nếu guest
    
    Contact::create([
      'listing_id' => $id,
      'user_id' => $userId, // null nếu guest
      'visitor_name' => $request->name,
      'visitor_phone' => $request->phone,
      'message' => $request->message,
      'contact_type' => 'message',
      'status' => 'pending',
    ]);
    
    $listing->increment('contacts_count');
    
    return response()->json(['success' => true, 'message' => 'Gửi liên hệ thành công']);
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
    $user = auth('partner')->user();
    
    $listings = Listing::where('user_id', $user->id)
      ->with(['category', 'city', 'district', 'package', 'primaryImage'])
      ->latest()
      ->paginate(20);
    
    return view('pages.my-listings', [
      'listings' => $listings,
    ]);
  }
}

