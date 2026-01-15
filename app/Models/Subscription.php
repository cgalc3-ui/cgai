<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\Translatable;

class Subscription extends Model
{
    use Translatable;
    protected $fillable = [
        'name',
        'name_en',
        'description',
        'description_en',
        'features',
        'features_en',
        'price',
        'duration_type',
        'max_debtors',
        'max_messages',
        'ai_enabled',
        'is_active',
        'is_pro',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_debtors' => 'integer',
        'max_messages' => 'integer',
        'ai_enabled' => 'boolean',
        'is_active' => 'boolean',
        'is_pro' => 'boolean',
        'features' => 'array',
        'features_en' => 'array',
        'duration_type' => 'string',
    ];

    /**
     * Get all subscription requests for this subscription
     */
    public function requests(): HasMany
    {
        return $this->hasMany(SubscriptionRequest::class);
    }

    /**
     * Get all user subscriptions for this subscription
     */
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get points pricing for this subscription
     */
    public function pointsPricing(): HasOne
    {
        return $this->hasOne(ServicePointsPricing::class)
            ->where('item_type', 'subscription')
            ->where('is_active', true);
    }

    /**
     * Scope to get active subscriptions only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get duration text based on current locale
     */
    public function getDurationTextAttribute(): string
    {
        $locale = app()->getLocale();
        
        $texts = [
            'ar' => [
                'month' => 'شهري',
                'year' => 'سنوي',
                'lifetime' => 'دائم',
            ],
            'en' => [
                'month' => 'Monthly',
                'year' => 'Yearly',
                'lifetime' => 'Lifetime',
            ],
        ];

        return $texts[$locale][$this->duration_type] ?? $this->duration_type;
    }
}
