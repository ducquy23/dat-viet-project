<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Ad Model: Quản lý quảng cáo
 * - Lưu trữ thông tin các banner quảng cáo
 * - Hỗ trợ nhiều vị trí hiển thị (top, sidebar, bottom)
 */
class Ad extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'title',
    'description',
    'image_url',
    'link_url',
    'link_text',
    'position',
    'sort_order',
    'is_active',
    'start_date',
    'end_date',
    'views_count',
    'clicks_count',
    'price',
    'pricing_type',
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'sort_order' => 'integer',
    'is_active' => 'boolean',
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'views_count' => 'integer',
    'clicks_count' => 'integer',
    'price' => 'decimal:2',
  ];

  /**
   * Scope: Chỉ lấy các quảng cáo đang hoạt động
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeActive($query)
  {
    return $query->where('is_active', true)
      ->where(function ($q) {
        $q->whereNull('start_date')
          ->orWhere('start_date', '<=', now());
      })
      ->where(function ($q) {
        $q->whereNull('end_date')
          ->orWhere('end_date', '>=', now());
      });
  }

  /**
   * Scope: Lọc theo vị trí
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $position
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfPosition($query, $position)
  {
    return $query->where('position', $position);
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
   * Tăng số lượt xem
   * @return void
   */
  public function incrementViews()
  {
    $this->increment('views_count');
  }

  /**
   * Tăng số lượt click
   * @return void
   */
  public function incrementClicks()
  {
    $this->increment('clicks_count');
  }

  /**
   * Kiểm tra xem quảng cáo có đang hiển thị không
   * @return bool
   */
  public function isDisplaying()
  {
    if (!$this->is_active) {
      return false;
    }

    if ($this->start_date && $this->start_date->isFuture()) {
      return false;
    }

    if ($this->end_date && $this->end_date->isPast()) {
      return false;
    }

    return true;
  }
}

