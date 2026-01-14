<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class Footer extends Model
{
    use Translatable;

    protected $fillable = [
        'logo',
        'logo_en',
        'description',
        'description_en',
        'email',
        'phone',
        'working_hours',
        'working_hours_en',
        'quick_links',
        'content_links',
        'support_links',
        'social_media',
        'copyright_text',
        'copyright_text_en',
        'is_active',
    ];

    protected $casts = [
        'quick_links' => 'array',
        'content_links' => 'array',
        'support_links' => 'array',
        'social_media' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}


