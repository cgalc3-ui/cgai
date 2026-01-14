<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class ConsultationBookingSection extends Model
{
    use Translatable;

    protected $fillable = [
        'heading',
        'heading_en',
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

