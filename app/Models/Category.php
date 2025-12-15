<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Category Model: Quản lý danh mục bất động sản
 * - Lưu trữ các loại đất (Đất thổ cư, Đất nông nghiệp, etc.)
 * - Có quan hệ với listings (tin đăng)
 */
class Category extends Model
{
  use HasFactory;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'name',        // Tên danh mục
    'slug',        // Slug cho URL
    'icon',        // Icon class (Bootstrap icon)
    'description', // Mô tả danh mục
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
   * Quan hệ: Một danh mục có nhiều tin đăng
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function listings()
  {
    return $this->hasMany(Listing::class);
  }

  /**
   * Scope: Chỉ lấy các danh mục đang hoạt động
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


