<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\Translatable;

class Consultation extends Model
{
    use Translatable;

    protected $fillable = [
        'category_id',
        'name',
        'name_en',
        'slug',
        'description',
        'description_en',
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

    /**
     * Get points pricing for this consultation
     */
    public function pointsPricing()
    {
        return $this->hasOne(ServicePointsPricing::class)
            ->where('item_type', 'consultation')
            ->where('is_active', true);
    }

    /**
     * Get all ratings/reviews for this consultation
     */
    public function reviews()
    {
        return $this->morphMany(Rating::class, 'ratable')->where('is_approved', true);
    }
}
