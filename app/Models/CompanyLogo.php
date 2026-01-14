<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class CompanyLogo extends Model
{
    use Translatable;

    protected $fillable = [
        'heading',
        'heading_en',
        'logos',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'logos' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
