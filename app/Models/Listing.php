<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Payment;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'city_id',
        'district_id',
        'package_id',
        'title',
        'description',
        'address',
        'latitude',
        'longitude',
        'price',
        'price_per_m2',
        'area',
        'front_width',
        'depth',
        'legal_status',
        'road_type',
        'road_width',
        'direction',
        'has_road_access',
        'planning_info',
        'deposit_online',
        'tags',
        'polygon_coordinates',
        'contact_name',
        'contact_phone',
        'contact_zalo',
        'status',
        'rejection_reason',
        'approved_at',
        'expires_at',
        'views_count',
        'favorites_count',
        'contacts_count',
        'slug',
        'meta_description',
    ];
    protected $casts = [
        'user_id' => 'integer',
        'category_id' => 'integer',
        'city_id' => 'integer',
        'district_id' => 'integer',
        'package_id' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'price' => 'decimal:2',
        'price_per_m2' => 'decimal:2',
        'area' => 'decimal:2',
        'front_width' => 'decimal:2',
        'depth' => 'decimal:2',
        'road_width' => 'decimal:2',
        'has_road_access' => 'boolean',
        'deposit_online' => 'boolean',
        'tags' => 'array',
        'polygon_coordinates' => 'array',
        'views_count' => 'integer',
        'favorites_count' => 'integer',
        'contacts_count' => 'integer',
        'approved_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($listing) {
            if (empty($listing->slug)) {
                $listing->slug = Str::slug($listing->title);
                $originalSlug = $listing->slug;
                $count = 1;
                while (static::where('slug', $listing->slug)->exists()) {
                    $listing->slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * @return BelongsTo
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * @return HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('sort_order');
    }

    /**
     * @return HasOne
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ListingImage::class)->where('is_primary', true);
    }

    /**
     * @return HasMany
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasOne
     */
    public function latestPayment(): HasOne
    {
        // Lấy giao dịch mới nhất của tin
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * @return HasMany
     */
    public function views(): HasMany
    {
        return $this->hasMany(ListingView::class);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeApproved($query): mixed
    {
        return $query->where('status', 'approved');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query): mixed
    {
        return $query->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * @param $query
     * @param $keyword
     * @return mixed
     */
    public function scopeSearch($query, $keyword): mixed
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('description', 'like', "%{$keyword}%")
                ->orWhere('address', 'like', "%{$keyword}%");
        });
    }

    /**
     * @return bool
     */
    public function isVip(): bool
    {
        return $this->package && $this->package->code === 'vip';
    }

    /**
     * Check if listing is active (approved and not expired)
     * @return bool
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'approved' && 
               ($this->expires_at === null || $this->expires_at > now());
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get price_per_m2 attribute - calculate if not set
     * @return float|null
     */
    public function getPricePerM2Attribute($value)
    {
        // If price_per_m2 is already set and not null, return it
        if ($value !== null && $value > 0) {
            return $value;
        }
        
        // Otherwise, calculate it from price and area
        if ($this->price && $this->area && $this->area > 0) {
            return $this->price / $this->area;
        }
        
        return null;
    }
}


