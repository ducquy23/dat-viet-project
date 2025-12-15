<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'image_path',
        'thumbnail_path',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'listing_id' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
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
     * @param $query
     * @return mixed
     */
    public function scopePrimary($query): mixed
    {
        return $query->where('is_primary', true);
    }

    /**
     * @return mixed|string
     */
    public function getImageUrlAttribute(): mixed
    {
        if (str_starts_with($this->image_path, 'http')) {
            return $this->image_path;
        }
        return asset('storage/' . $this->image_path);
    }

    /**
     * @return mixed|string
     */
    public function getThumbnailUrlAttribute(): mixed
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


