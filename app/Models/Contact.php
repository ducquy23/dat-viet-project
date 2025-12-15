<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Contact Model: Quản lý liên hệ với người đăng tin
 * - Lưu trữ thông tin liên hệ từ người xem tin đăng
 * - Có thể từ user đã đăng nhập hoặc visitor
 */
class Contact extends Model
{
  use HasFactory;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'listing_id',
    'user_id',
    'visitor_name',
    'visitor_phone',
    'visitor_email',
    'contact_type',
    'message',
    'status',
    'notes',
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'listing_id' => 'integer',
    'user_id' => 'integer',
  ];

  /**
   * Quan hệ: Một liên hệ thuộc về một tin đăng
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function listing()
  {
    return $this->belongsTo(Listing::class);
  }

  /**
   * Quan hệ: Một liên hệ thuộc về một user (nếu user đã đăng nhập)
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Scope: Lọc theo loại liên hệ
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $type
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfType($query, $type)
  {
    return $query->where('contact_type', $type);
  }

  /**
   * Scope: Lọc theo trạng thái
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $status
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfStatus($query, $status)
  {
    return $query->where('status', $status);
  }

  /**
   * Lấy tên người liên hệ
   * @return string
   */
  public function getContactNameAttribute()
  {
    if ($this->user) {
      return $this->user->name ?? $this->user->phone;
    }
    return $this->visitor_name ?? 'Khách';
  }

  /**
   * Lấy số điện thoại liên hệ
   * @return string
   */
  public function getContactPhoneAttribute()
  {
    if ($this->user) {
      return $this->user->phone ?? '';
    }
    return $this->visitor_phone ?? '';
  }
}


