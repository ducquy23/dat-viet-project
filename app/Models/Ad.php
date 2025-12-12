<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use HasFactory, SoftDeletes;

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
     * @param $query
     * @return mixed
     */
    public function scopeActive($query): mixed
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
     * @param $query
     * @param $position
     * @return mixed
     */
    public function scopeOfPosition($query, $position): mixed
    {
        return $query->where('position', $position);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOrdered($query): mixed
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return void
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * @return void
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks_count');
    }

    /**
     * @return bool
     */
    public function isDisplaying(): bool
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

    /**
     * @return mixed|string|null
     */
    public function getFullImageUrlAttribute(): mixed
    {
        if (!$this->attributes['image_url']) {
            return null;
        }

        $imageUrl = $this->attributes['image_url'];

        if (str_starts_with($imageUrl, 'http')) {
            return $imageUrl;
        }

        return asset('storage/' . $imageUrl);
    }
}

