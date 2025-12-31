<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'fixed_price',
        'is_active',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'fixed_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($consultation) {
            if (empty($consultation->slug)) {
                $consultation->slug = \Illuminate\Support\Str::slug($consultation->name);
            }
        });

        static::updating(function ($consultation) {
            if ($consultation->isDirty('name') && empty($consultation->slug)) {
                $consultation->slug = \Illuminate\Support\Str::slug($consultation->name);
            }
        });
    }

    /**
     * Get the category (specialization) for this consultation
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all bookings for this consultation
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get price attribute
     */
    public function getPriceAttribute(): float
    {
        return (float) $this->fixed_price;
    }
}
