<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadyAppOrder extends Model
{
    protected $fillable = [
        'user_id',
        'ready_app_id',
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
        'ready_app_id' => 'integer',
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
     * Get the app for this order
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(ReadyApp::class, 'ready_app_id');
    }

    /**
     * Get the admin who processed this order
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
