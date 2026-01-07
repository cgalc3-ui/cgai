<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsSetting extends Model
{
    protected $fillable = [
        'points_per_riyal',
        'is_active',
    ];

    protected $casts = [
        'points_per_riyal' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the current active settings
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first() ?? static::first();
    }
}
