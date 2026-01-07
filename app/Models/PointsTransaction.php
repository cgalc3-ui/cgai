<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'booking_id',
        'type',
        'points',
        'amount_paid',
        'payment_id',
        'description',
        'balance_before',
        'balance_after',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /**
     * Get the user that owns this transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet for this transaction
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the booking for this transaction
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
