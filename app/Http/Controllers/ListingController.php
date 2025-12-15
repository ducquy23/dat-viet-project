<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Contact;
use App\Models\ListingImage;
use App\Models\ListingView;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ListingController extends Controller
{

    /**
     * @param $slug
     * @return Factory|View
     */
    public function show($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $listing = Listing::where('slug', $slug)
            ->active()
            ->with(['user', 'category', 'city', 'district', 'package', 'images' => function ($query) {
                $query->orderBy('is_primary', 'desc')->orderBy('sort_order');
            }])
            ->firstOrFail();

        // Tăng view count
        $listing->increment('views_count');

        // Ghi log view
        ListingView::create([
            'listing_id' => $listing->id,
            'user_id' => auth('partner')->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now(),
        ]);

        $isFavorited = false;
        $user = auth('partner')->user();
        if ($user) {
            $isFavorited = Favorite::where('user_id', $user->id)
                ->where('listing_id', $listing->id)
                ->exists();
        }

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
     * @param $categorySlug
     * @return Factory|View
     */
    public function category($categorySlug): Factory|View
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
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function toggleFavorite(Request $request, $id): JsonResponse
    {
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
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function contact(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'nullable|string|max:1000',
        ]);

        $listing = Listing::findOrFail($id);
        $userId = auth('partner')->id();

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
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'title' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'front_width' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'road_width' => 'nullable|numeric|min:0',
            'legal_status' => 'nullable|string|max:255',
            'road_type' => 'nullable|string|max:255',
            'direction' => 'nullable|string|max:255',
            'has_road_access' => 'nullable|boolean',
            'description' => 'nullable|string|max:5000',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_zalo' => 'nullable|string|max:255',
            'package_id' => 'required|exists:packages,id',
            'images.*' => 'nullable|image|max:5120', // 5MB
        ], [
            'category_id.required' => 'Vui lòng chọn loại đất',
            'city_id.required' => 'Vui lòng chọn tỉnh/thành phố',
            'title.required' => 'Vui lòng nhập tiêu đề',
            'address.required' => 'Vui lòng nhập địa chỉ',
            'latitude.required' => 'Vui lòng chọn vị trí trên bản đồ',
            'longitude.required' => 'Vui lòng chọn vị trí trên bản đồ',
            'price.required' => 'Vui lòng nhập giá bán',
            'area.required' => 'Vui lòng nhập diện tích',
            'contact_name.required' => 'Vui lòng nhập tên người liên hệ',
            'contact_phone.required' => 'Vui lòng nhập số điện thoại',
        ]);

        DB::beginTransaction();
        try {
            // Tính toán giá trị
            $pricePerM2 = $request->area > 0 ? ($request->price * 1000000) / $request->area : null;

            // Tạo slug từ title
            $baseSlug = Str::slug($request->title);
            $slug = $baseSlug;
            $counter = 1;
            while (Listing::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $listing = Listing::create([
                'user_id' => auth('partner')->id(),
                'category_id' => $request->category_id,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'package_id' => $request->package_id ?? 1,
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'price' => $request->price * 1000000, // Convert triệu đồng sang đồng
                'price_per_m2' => $pricePerM2,
                'area' => $request->area,
                'front_width' => $request->front_width,
                'depth' => $request->depth,
                'road_width' => $request->road_width,
                'legal_status' => $request->legal_status,
                'road_type' => $request->road_type,
                'direction' => $request->direction,
                'has_road_access' => $request->has('has_road_access') ? true : false,
                'contact_name' => $request->contact_name,
                'contact_phone' => $request->contact_phone,
                'contact_zalo' => $request->contact_zalo,
                'slug' => $slug,
                'status' => 'pending', // Mặc định chờ duyệt
            ]);

            // Upload images
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $imageCount = 0;

                foreach ($images as $index => $image) {
                    if ($imageCount >= 5) break; // Tối đa 5 ảnh

                    $path = $image->store('listings/gallery', 'public');

                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0, // Ảnh đầu tiên là primary
                        'sort_order' => $index,
                    ]);

                    $imageCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tin đăng đã được gửi và đang chờ duyệt',
                'redirect_to' => route('listings.my-listings')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Có lỗi xảy ra khi đăng tin: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return Factory|View|JsonResponse
     */
    public function myListings(Request $request): Factory|View|JsonResponse
    {
        $user = auth('partner')->user();

        $listings = Listing::where('user_id', $user->id)
            ->with(['category', 'city', 'district', 'package', 'primaryImage'])
            ->latest()
            ->paginate(20);

        if ($request->wantsJson() || $request->get('format') === 'json') {
            return response()->json([
                'listings' => $listings->map(function ($listing) {
                    return [
                        'id' => $listing->id,
                        'title' => $listing->title,
                        'address' => $listing->address,
                        'price' => number_format($listing->price) . ' triệu',
                        'size' => $listing->area . 'm²',
                        'vip' => $listing->isVip(),
                        'is_vip' => $listing->isVip(),
                        'status' => $listing->status,
                        'type' => $listing->category?->slug ?? 'thocu',
                        'slug' => $listing->slug,
                        'created_at' => $listing->created_at->format('d/m/Y'),
                    ];
                }),
                'pagination' => [
                    'current_page' => $listings->currentPage(),
                    'last_page' => $listings->lastPage(),
                    'per_page' => $listings->perPage(),
                    'total' => $listings->total(),
                ],
            ]);
        }

        return view('pages.my-listings', [
            'listings' => $listings,
        ]);
    }
}

