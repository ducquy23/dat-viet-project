<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * City Model: Quản lý thông tin tỉnh/thành phố
 * - Lưu trữ danh sách các tỉnh/thành phố
 * - Có quan hệ với districts (quận/huyện)
 */
class City extends Model
{
  use HasFactory;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'name',        // Tên tỉnh/thành phố
    'code',        // Mã code (hcm, hn, etc.)
    'slug',        // Slug cho URL
    'sort_order',  // Thứ tự sắp xếp
    'is_active',   // Trạng thái hoạt động
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'is_active' => 'boolean',
    'sort_order' => 'integer',
  ];

  /**
   * Quan hệ: Một thành phố có nhiều quận/huyện
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function districts()
  {
    return $this->hasMany(District::class);
  }

  /**
   * Quan hệ: Một thành phố có nhiều tin đăng
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function listings()
  {
    return $this->hasMany(Listing::class);
  }

  /**
   * Scope: Chỉ lấy các thành phố đang hoạt động
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeActive($query)
  {
    return $query->where('is_active', true);
  }

  /**
   * Scope: Sắp xếp theo thứ tự
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOrdered($query)
  {
    return $query->orderBy('sort_order')->orderBy('name');
  }
}

