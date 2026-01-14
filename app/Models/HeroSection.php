<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class HeroSection extends Model
{
    use Translatable;

    protected $fillable = [
        'heading',
        'heading_en',
        'subheading',
        'subheading_en',
        'description',
        'description_en',
        'background_image',
        'buttons',
        'is_active',
    ];

    protected $casts = [
        'buttons' => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
