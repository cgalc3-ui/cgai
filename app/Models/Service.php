<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\Translatable;

class Service extends Model
{
    use Translatable;

    protected $fillable = [
        'sub_category_id',
        'name',
        'name_en',
        'slug',
        'description',
        'description_en',
        'hourly_rate',
        'is_active',
    ];

    protected $casts = [
        'sub_category_id' => 'integer',
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
     * Get the category (specialization) through sub category
     */
    public function getCategoryAttribute()
    {
        return $this->subCategory?->category;
    }

    /**
     * Get category_id (for backward compatibility)
     */
    public function getCategoryIdAttribute()
    {
        return $this->subCategory?->category_id;
    }

    /**
     * Get specialization_id (for backward compatibility - returns category_id)
     */
    public function getSpecializationIdAttribute()
    {
        return $this->subCategory?->category_id;
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

    /**
     * Get points pricing for this service
     */
    public function pointsPricing()
    {
        return $this->hasOne(ServicePointsPricing::class)
            ->where('item_type', 'service')
            ->where('is_active', true);
    }
}
