<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class SubscriptionsSection extends Model
{
    use Translatable;

    protected $table = 'subscriptions_section';

    protected $fillable = [
        'title',
        'title_en',
        'description',
        'description_en',
        'background_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
