<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ListingImage Model: Quản lý hình ảnh của tin đăng
 * - Lưu trữ đường dẫn hình ảnh và thumbnail
 * - Mỗi hình ảnh thuộc về một tin đăng
 */
class ListingImage extends Model
{
  use HasFactory;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'listing_id',      // ID tin đăng
    'image_path',      // Đường dẫn hình ảnh
    'thumbnail_path',  // Đường dẫn thumbnail
    'sort_order',      // Thứ tự sắp xếp
    'is_primary',      // Có phải ảnh chính không
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'listing_id' => 'integer',
    'sort_order' => 'integer',
    'is_primary' => 'boolean',
  ];

  /**
   * Quan hệ: Một hình ảnh thuộc về một tin đăng
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function listing()
  {
    return $this->belongsTo(Listing::class);
  }

  /**
   * Scope: Sắp xếp theo thứ tự
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOrdered($query)
  {
    return $query->orderBy('sort_order')->orderBy('id');
  }

  /**
   * Scope: Chỉ lấy ảnh chính
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopePrimary($query)
  {
    return $query->where('is_primary', true);
  }

  /**
   * Lấy URL đầy đủ của hình ảnh
   * @return string
   */
  public function getImageUrlAttribute()
  {
    if (str_starts_with($this->image_path, 'http')) {
      return $this->image_path;
    }
    return asset('storage/' . $this->image_path);
  }

  /**
   * Lấy URL đầy đủ của thumbnail
   * @return string
   */
  public function getThumbnailUrlAttribute()
  {
    if ($this->thumbnail_path) {
      if (str_starts_with($this->thumbnail_path, 'http')) {
        return $this->thumbnail_path;
      }
      return asset('storage/' . $this->thumbnail_path);
    }
    return $this->image_url;
  }
}

