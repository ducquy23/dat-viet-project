<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Package Model: Quản lý các gói đăng tin
 * - Lưu trữ thông tin các gói (Thường, VIP)
 * - Có quan hệ với listings và payments
 */
class Package extends Model
{
  use HasFactory;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'name',         // Tên gói (Thường, VIP)
    'code',         // Mã code (normal, vip)
    'description',  // Mô tả gói
    'price',        // Giá gói (0 = miễn phí)
    'duration_days', // Số ngày hiển thị
    'priority',     // Độ ưu tiên hiển thị
    'features',     // Tính năng (JSON)
    'is_active',    // Trạng thái hoạt động
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'price' => 'decimal:2',
    'duration_days' => 'integer',
    'priority' => 'integer',
    'features' => 'array',
    'is_active' => 'boolean',
  ];

  /**
   * Quan hệ: Một gói có nhiều tin đăng
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function listings()
  {
    return $this->hasMany(Listing::class);
  }

  /**
   * Quan hệ: Một gói có nhiều thanh toán
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function payments()
  {
    return $this->hasMany(Payment::class);
  }

  /**
   * Scope: Chỉ lấy các gói đang hoạt động
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  /**
   * Kiểm tra xem gói có miễn phí không
   * @return bool
   */
  public function isFree()
  {
    return $this->price == 0;
  }
}

