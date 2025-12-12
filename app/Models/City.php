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
        'name',
        'type',
        'code',
    ];

    /**
     * Các trường được cast sang kiểu dữ liệu cụ thể
     */
    protected $casts = [];

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

    // Không còn quan hệ wards theo cấu trúc mới

    /**
     * Scope: Chỉ lấy các thành phố đang hoạt động
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        // Giữ tương thích cũ: không lọc gì (vì bỏ cột is_active)
        return $query;
    }

    /**
     * Scope: Sắp xếp theo thứ tự
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}

