<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_type',
        'max_debtors',
        'max_messages',
        'ai_enabled',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_debtors' => 'integer',
        'max_messages' => 'integer',
        'ai_enabled' => 'boolean',
        'is_active' => 'boolean',
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
     * Scope to get active subscriptions only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get duration text in Arabic
     */
    public function getDurationTextAttribute(): string
    {
        return match($this->duration_type) {
            'month' => 'شهري',
            'year' => 'سنوي',
            'lifetime' => 'دائم',
            default => $this->duration_type,
        };
    }
}
