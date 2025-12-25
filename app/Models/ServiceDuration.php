<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDuration extends Model
{
    protected $fillable = [
        'service_id',
        'duration_type',
        'duration_value',
        'price',
        'is_active',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'duration_value' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the service that owns this duration
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get formatted duration display
     */
    public function getFormattedDurationAttribute(): string
    {
        $typeLabels = [
            'hour' => 'ساعة',
            'day' => 'يوم',
            'week' => 'أسبوع',
        ];

        $typeLabel = $typeLabels[$this->duration_type] ?? $this->duration_type;
        return "{$this->duration_value} {$typeLabel}";
    }
}
