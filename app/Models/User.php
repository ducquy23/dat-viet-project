<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
        'phone_verified',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone_verified' => 'boolean',
        ];
    }

    /**
     * Quan hệ: Một user có nhiều tin đăng
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Quan hệ: Một user có nhiều yêu thích
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Quan hệ: Một user có nhiều liên hệ
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Quan hệ: Một user có nhiều thanh toán
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Quan hệ: Một user có nhiều lượt xem
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listingViews()
    {
        return $this->hasMany(ListingView::class);
    }

    /**
     * Kiểm tra xem user có yêu thích tin đăng này không
     * @param int $listingId
     * @return bool
     */
    public function hasFavorited($listingId)
    {
        return $this->favorites()->where('listing_id', $listingId)->exists();
    }

    /**
     * Kiểm tra xem user có phải admin không
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Kiểm tra xem user có phải đối tác không
     * @return bool
     */
    public function isPartner()
    {
        return $this->role === 'user' || $this->role === 'moderator';
    }

    /**
     * Kiểm tra xem user có thể truy cập Filament admin panel không
     * Chỉ admin mới được truy cập
     * @return bool
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->isAdmin();
    }
}
