<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionRequest extends Model
{
    protected $table = 'subscription_requests';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'payment_proof',
        'status',
        'admin_notes',
        'approved_at',
        'rejected_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [];

    /**
     * Get the user that requested the subscription
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the admin who approved/rejected the request
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to get pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Add global scope to always select specific columns
        static::addGlobalScope('selectColumns', function ($builder) {
            $builder->select([
                'subscription_requests.id',
                'subscription_requests.user_id',
                'subscription_requests.subscription_id',
                'subscription_requests.payment_proof',
                'subscription_requests.status',
                'subscription_requests.admin_notes',
                'subscription_requests.approved_at',
                'subscription_requests.rejected_at',
                'subscription_requests.approved_by',
                'subscription_requests.created_at',
                'subscription_requests.updated_at'
            ]);
        });
    }
}
