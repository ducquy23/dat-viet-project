<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * District Model: Quản lý thông tin quận/huyện
 * - Lưu trữ danh sách các quận/huyện
 * - Thuộc về một thành phố (city)
 */
class District extends Model
{
  use HasFactory;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'city_id',     // ID thành phố
    'name',        // Tên quận/huyện
    'type',        // Loại: Quận / Huyện / Phường (theo yêu cầu)
    'code',        // Mã code hành chính
    'sort_order',      // Thứ tự
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'city_id' => 'integer',
    'sort_order' => 'integer',
  ];

  /**
   * Quan hệ: Một quận/huyện thuộc về một thành phố
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function city()
  {
    return $this->belongsTo(City::class);
  }

  /**
   * Quan hệ: Một quận/huyện có nhiều tin đăng
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function listings()
  {
    return $this->hasMany(Listing::class);
  }

  // Không còn quan hệ wards theo cấu trúc mới

  /**
   * Scope: Chỉ lấy các quận/huyện đang hoạt động
   * @param Builder $query
   * @return Builder
   */
  public function scopeActive($query)
  {
    // Giữ tương thích cũ: không lọc gì (vì bỏ cột is_active)
    return $query;
  }

  /**
   * Scope: Sắp xếp theo thứ tự
   * @param Builder $query
   * @return Builder
   */
  public function scopeOrdered($query)
  {
    return $query->orderBy('sort_order')->orderBy('name');
  }
}

