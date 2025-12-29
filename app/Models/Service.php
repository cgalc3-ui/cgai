<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'sub_category_id',
        'specialization_id',
        'name',
        'slug',
        'description',
        'hourly_rate',
        'is_active',
    ];

    protected $casts = [
        'sub_category_id' => 'integer',
        'specialization_id' => 'integer',
        'hourly_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'hourly_rate',
    ];

    protected $appends = [
        'price',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = \Illuminate\Support\Str::slug($service->name);
            }
        });

        static::updating(function ($service) {
            if ($service->isDirty('name') && empty($service->slug)) {
                $service->slug = \Illuminate\Support\Str::slug($service->name);
            }
        });
    }

    /**
     * Get the sub category that owns this service
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Get the category through sub category
     */
    public function getCategoryAttribute()
    {
        return $this->subCategory?->category;
    }

    /**
     * Get the specialization that this service belongs to
     */
    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    /**
     * Get price attribute (alias for hourly_rate)
     */
    public function getPriceAttribute(): ?float
    {
        return $this->hourly_rate !== null ? (float) $this->hourly_rate : null;
    }

    /**
     * Get hourly rate
     */
    public function getHourlyRate(): ?float
    {
        return $this->getPriceAttribute();
    }
}
