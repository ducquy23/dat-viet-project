<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'code',
    ];

    protected $casts = [];

    /**
     * @return HasMany
     */
    public function districts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(District::class);
    }

    /**
     * @return HasMany
     */
    public function listings(): HasMany
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
        return $query->orderBy('name');
    }
}

