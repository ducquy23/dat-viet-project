<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_slogan',
        'hotline',
        'zalo',
        'support_email',
        'logo_url',
        'og_image_url',
        'vip_limit',
        'vip_sort',
        'support_message',
    ];
}

