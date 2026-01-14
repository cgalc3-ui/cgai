<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiServiceOrder extends Model
{
    protected $fillable = [
        'user_id',
        'ai_service_id',
        'price',
        'currency',
        'status',
        'notes',
        'contact_preference',
        'pricing_plan_id',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'ai_service_id' => 'integer',
        'price' => 'decimal:2',
        'pricing_plan_id' => 'integer',
        'processed_by' => 'integer',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user who made this order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the service for this order
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(AiService::class, 'ai_service_id');
    }

    /**
     * Get the admin who processed this order
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
