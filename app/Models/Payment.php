<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Payment Model: Quản lý thanh toán
 * - Lưu trữ thông tin thanh toán cho các gói đăng tin
 * - Hỗ trợ nhiều phương thức thanh toán
 */
class Payment extends Model
{
  use HasFactory;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'user_id',
    'listing_id',
    'package_id',
    'transaction_id',
    'amount',
    'currency',
    'payment_method',
    'status',
    'payment_info',
    'notes',
    'paid_at',
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'user_id' => 'integer',
    'listing_id' => 'integer',
    'package_id' => 'integer',
    'amount' => 'decimal:2',
    'payment_info' => 'array',
    'paid_at' => 'datetime',
  ];

  /**
   * Boot method: Tự động tạo transaction_id khi tạo mới
   */
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($payment) {
      if (empty($payment->transaction_id)) {
        $payment->transaction_id = 'TXN' . strtoupper(Str::random(12)) . time();
      }
    });
  }

  /**
   * Quan hệ: Một thanh toán thuộc về một user
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Quan hệ: Một thanh toán thuộc về một tin đăng
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function listing()
  {
    return $this->belongsTo(Listing::class);
  }

  /**
   * Quan hệ: Một thanh toán thuộc về một gói
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function package()
  {
    return $this->belongsTo(Package::class);
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
   * Scope: Lọc theo phương thức thanh toán
   * @param \Illuminate\Database\Eloquent\Builder $query
   * @param string $method
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOfMethod($query, $method)
  {
    return $query->where('payment_method', $method);
  }

  /**
   * Kiểm tra xem thanh toán đã hoàn thành chưa
   * @return bool
   */
  public function isCompleted()
  {
    return $this->status === 'completed';
  }

  /**
   * Kiểm tra xem thanh toán đang chờ xử lý không
   * @return bool
   */
  public function isPending()
  {
    return $this->status === 'pending';
  }
}


