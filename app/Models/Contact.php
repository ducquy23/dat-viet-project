<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'visitor_name',
        'visitor_phone',
        'visitor_email',
        'contact_type',
        'message',
        'status',
        'notes',
    ];

    protected $casts = [
        'listing_id' => 'integer',
        'user_id' => 'integer',
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
     * @param $type
     * @return mixed
     */
    public function scopeOfType($query, $type): mixed
    {
        return $query->where('contact_type', $type);
    }

    /**
     * @param $query
     * @param $status
     * @return mixed
     */
    public function scopeOfStatus($query, $status): mixed
    {
        return $query->where('status', $status);
    }

    /**
     * @return mixed|string
     */
    public function getContactNameAttribute(): mixed
    {
        if ($this->user) {
            return $this->user->name ?? $this->user->phone;
        }
        return $this->visitor_name ?? 'Khách';
    }

    /**
     * @return mixed|string
     */
    public function getContactPhoneAttribute(): mixed
    {
        if ($this->user) {
            return $this->user->phone ?? '';
        }
        return $this->visitor_phone ?? '';
    }
}

