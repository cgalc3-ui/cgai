<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiServiceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'use_case',
        'expected_features',
        'budget_range',
        'custom_budget',
        'urgency',
        'deadline',
        'status',
        'estimated_price',
        'final_price',
        'currency',
        'contact_preference',
        'admin_notes',
        'processed_by',
        'processed_at',
        'quoted_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'category_id' => 'integer',
        'expected_features' => 'array',
        'custom_budget' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'deadline' => 'date',
        'processed_by' => 'integer',
        'processed_at' => 'datetime',
        'quoted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who made this request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category for this request
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AiServiceCategory::class, 'category_id');
    }

    /**
     * Get the admin who processed this request
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get all attachments for this request
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(AiServiceAttachment::class)->orderBy('order');
    }
}
