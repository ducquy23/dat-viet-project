<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',        // Tên danh mục
        'slug',        // Slug cho URL
        'icon',        // Icon class (Bootstrap icon)
        'description', // Mô tả danh mục
        'sort_order',  // Thứ tự sắp xếp
        'is_active',   // Trạng thái hoạt động
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * @return HasMany
     */
    public function listings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOrdered($query): mixed
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

