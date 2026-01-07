<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePointsPricing extends Model
{
    protected $table = 'service_points_pricing';

    protected $fillable = [
        'service_id',
        'consultation_id',
        'item_type',
        'points_price',
        'is_active',
    ];

    protected $casts = [
        'points_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the consultation
     */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * Get pricing for a service or consultation
     */
    public static function getPricing($itemType, $itemId)
    {
        if ($itemType === 'service') {
            return static::where('service_id', $itemId)
                ->where('item_type', 'service')
                ->where('is_active', true)
                ->first();
        } else {
            return static::where('consultation_id', $itemId)
                ->where('item_type', 'consultation')
                ->where('is_active', true)
                ->first();
        }
    }
}
