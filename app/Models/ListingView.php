<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingView extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'listing_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referer',
        'viewed_at',
    ];

    protected $casts = [
        'listing_id' => 'integer',
        'user_id' => 'integer',
        'viewed_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function listing(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $query
     * @param $listingId
     * @return mixed
     */
    public function scopeOfListing($query, $listingId): mixed
    {
        return $query->where('listing_id', $listingId);
    }

    /**
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeOfUser($query, $userId): mixed
    {
        return $query->where('user_id', $userId);
    }

    /**
     * @param $query
     * @param $period
     * @return mixed
     */
    public function scopeOfPeriod($query, $period): mixed
    {
        return match ($period) {
            'today' => $query->whereDate('viewed_at', today()),
            'week' => $query->where('viewed_at', '>=', now()->subWeek()),
            'month' => $query->where('viewed_at', '>=', now()->subMonth()),
            'year' => $query->where('viewed_at', '>=', now()->subYear()),
            default => $query,
        };
    }
}

