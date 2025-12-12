<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
        'package_id',
        'transaction_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'payment_info',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'listing_id' => 'integer',
        'package_id' => 'integer',
        'amount' => 'decimal:2',
        'payment_info' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->transaction_id)) {
                $payment->transaction_id = 'TXN' . strtoupper(Str::random(12)) . time();
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
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * @return BelongsTo
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
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
     * @param $query
     * @param $method
     * @return mixed
     */
    public function scopeOfMethod($query, $method): mixed
    {
        return $query->where('payment_method', $method);
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}

