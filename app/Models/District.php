<?php

namespace App\Models;

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
    'code',        // Mã code (phunhuan, binhthanh, etc.)
    'slug',        // Slug cho URL
    'sort_order',  // Thứ tự sắp xếp
    'is_active',   // Trạng thái hoạt động
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'city_id' => 'integer',
    'is_active' => 'boolean',
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

  /**
   * Scope: Chỉ lấy các quận/huyện đang hoạt động
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

