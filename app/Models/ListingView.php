<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ListingView Model: Quản lý lượt xem tin đăng
 * - Lưu trữ thông tin chi tiết về lượt xem
 * - Hỗ trợ tracking cho cả user đã đăng nhập và visitor
 */
class ListingView extends Model
{
  use HasFactory;

  /**
   * Tắt timestamps tự động (sử dụng viewed_at thay vì created_at)
   */
  public $timestamps = false;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'listing_id',
    'user_id',
    'ip_address',
    'user_agent',
    'referer',
    'viewed_at',
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'listing_id' => 'integer',
    'user_id' => 'integer',
    'viewed_at' => 'datetime',
  ];

  /**
   * Quan hệ: Một lượt xem thuộc về một tin đăng
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function listing()
  {
    return $this->belongsTo(Listing::class);
  }

  /**
   * Quan hệ: Một lượt xem thuộc về một user (nếu user đã đăng nhập)
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Scope: Lọc theo tin đăng
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param int $listingId
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfListing($query, $listingId)
  {
    return $query->where('listing_id', $listingId);
  }

  /**
   * Scope: Lọc theo user
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param int $userId
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfUser($query, $userId)
  {
    return $query->where('user_id', $userId);
  }

  /**
   * Scope: Lọc theo khoảng thời gian
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $period (today, week, month, year)
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfPeriod($query, $period)
  {
    return match ($period) {
      'today' => $query->whereDate('viewed_at', today()),
      'week' => $query->where('viewed_at', '>=', now()->subWeek()),
      'month' => $query->where('viewed_at', '>=', now()->subMonth()),
      'year' => $query->where('viewed_at', '>=', now()->subYear()),
      default => $query,
    };
  }
}


