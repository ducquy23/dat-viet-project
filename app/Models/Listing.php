<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Listing Model: Quản lý tin đăng bất động sản
 * - Lưu trữ thông tin chi tiết về các lô đất
 * - Có quan hệ với nhiều models khác (user, category, city, district, etc.)
 */
class Listing extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'user_id',
    'category_id',
    'city_id',
    'district_id',
    'package_id',
    'title',
    'description',
    'address',
    'latitude',
    'longitude',
    'price',
    'price_per_m2',
    'area',
    'front_width',
    'depth',
    'legal_status',
    'road_type',
    'road_width',
    'direction',
    'has_road_access',
    'planning_info',
    'deposit_online',
    'tags',
    'polygon_coordinates',
    'contact_name',
    'contact_phone',
    'contact_zalo',
    'status',
    'rejection_reason',
    'approved_at',
    'expires_at',
    'views_count',
    'favorites_count',
    'contacts_count',
    'slug',
    'meta_description',
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'user_id' => 'integer',
    'category_id' => 'integer',
    'city_id' => 'integer',
    'district_id' => 'integer',
    'package_id' => 'integer',
    'latitude' => 'decimal:8',
    'longitude' => 'decimal:8',
    'price' => 'decimal:2',
    'price_per_m2' => 'decimal:2',
    'area' => 'decimal:2',
    'front_width' => 'decimal:2',
    'depth' => 'decimal:2',
    'road_width' => 'decimal:2',
    'has_road_access' => 'boolean',
    'deposit_online' => 'boolean',
    'tags' => 'array',
    'polygon_coordinates' => 'array',
    'views_count' => 'integer',
    'favorites_count' => 'integer',
    'contacts_count' => 'integer',
    'approved_at' => 'datetime',
    'expires_at' => 'datetime',
  ];

  /**
   * Boot method: Tự động tạo slug khi tạo mới
   */
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($listing) {
      if (empty($listing->slug)) {
        $listing->slug = Str::slug($listing->title);
        // Đảm bảo slug là unique
        $originalSlug = $listing->slug;
        $count = 1;
        while (static::where('slug', $listing->slug)->exists()) {
          $listing->slug = $originalSlug . '-' . $count;
          $count++;
        }
      }
    });
  }

  /**
   * Quan hệ: Một tin đăng thuộc về một user
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Quan hệ: Một tin đăng thuộc về một danh mục
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  /**
   * Quan hệ: Một tin đăng thuộc về một thành phố
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function city()
  {
    return $this->belongsTo(City::class);
  }

  /**
   * Quan hệ: Một tin đăng thuộc về một quận/huyện
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function district()
  {
    return $this->belongsTo(District::class);
  }

  /**
   * Quan hệ: Một tin đăng thuộc về một gói
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function package()
  {
    return $this->belongsTo(Package::class);
  }

  /**
   * Quan hệ: Một tin đăng có nhiều hình ảnh
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function images()
  {
    return $this->hasMany(ListingImage::class)->orderBy('sort_order');
  }

  /**
   * Quan hệ: Một tin đăng có một hình ảnh chính
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function primaryImage()
  {
    return $this->hasOne(ListingImage::class)->where('is_primary', true);
  }

  /**
   * Quan hệ: Một tin đăng có nhiều yêu thích
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function favorites()
  {
    return $this->hasMany(Favorite::class);
  }

  /**
   * Quan hệ: Một tin đăng có nhiều liên hệ
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function contacts()
  {
    return $this->hasMany(Contact::class);
  }

  /**
   * Quan hệ: Một tin đăng có nhiều lượt xem
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function views()
  {
    return $this->hasMany(ListingView::class);
  }

  /**
   * Scope: Chỉ lấy các tin đăng đã được duyệt
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeApproved($query)
  {
    return $query->where('status', 'approved');
  }

  /**
   * Scope: Chỉ lấy các tin đăng chưa hết hạn
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeActive($query)
  {
    return $query->where('status', 'approved')
      ->where(function ($q) {
        $q->whereNull('expires_at')
          ->orWhere('expires_at', '>', now());
      });
  }

  /**
   * Scope: Tìm kiếm theo từ khóa
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $keyword
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeSearch($query, $keyword)
  {
    return $query->where(function ($q) use ($keyword) {
      $q->where('title', 'like', "%{$keyword}%")
        ->orWhere('description', 'like', "%{$keyword}%")
        ->orWhere('address', 'like', "%{$keyword}%");
    });
  }

  /**
   * Kiểm tra xem tin đăng có phải VIP không
   * @return bool
   */
  public function isVip()
  {
    return $this->package && $this->package->code === 'vip';
  }

  /**
   * Kiểm tra xem tin đăng có hết hạn không
   * @return bool
   */
  public function isExpired()
  {
    return $this->expires_at && $this->expires_at->isPast();
  }
}

