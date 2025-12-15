<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Favorite Model: Quản lý yêu thích tin đăng
 * - Lưu trữ thông tin user yêu thích tin đăng nào
 * - Quan hệ many-to-many giữa User và Listing
 */
class Favorite extends Model
{
  use HasFactory;

  /**
   * Các trường có thể gán hàng loạt (mass assignment)
   */
  protected $fillable = [
    'user_id',    // ID user
    'listing_id', // ID tin đăng
  ];

  /**
   * Các trường được cast sang kiểu dữ liệu cụ thể
   */
  protected $casts = [
    'user_id' => 'integer',
    'listing_id' => 'integer',
  ];

  /**
   * Quan hệ: Một yêu thích thuộc về một user
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Quan hệ: Một yêu thích thuộc về một tin đăng
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function listing()
  {
    return $this->belongsTo(Listing::class);
  }
}


