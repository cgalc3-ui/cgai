<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'sub_category_id',
        'specialization_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'sub_category_id' => 'integer',
        'specialization_id' => 'integer',
        'is_active' => 'boolean',
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

    public function durations(): HasMany
    {
        return $this->hasMany(ServiceDuration::class);
    }

    public function activeDurations(): HasMany
    {
        return $this->hasMany(ServiceDuration::class)->where('is_active', true);
    }
}
