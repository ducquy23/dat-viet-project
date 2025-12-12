<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'type',
        'code',
        'sort_order',
    ];

    protected $casts = [
        'city_id' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

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
        return $query;
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

