<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get the user that owns this wallet
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transactions for this wallet
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PointsTransaction::class);
    }

    /**
     * Add points to wallet
     */
    public function addPoints(float $points, string $type = 'purchase', $bookingId = null, $amountPaid = null, $paymentId = null, $description = null): PointsTransaction
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $points);
        $this->refresh();

        return PointsTransaction::create([
            'user_id' => $this->user_id,
            'wallet_id' => $this->id,
            'booking_id' => $bookingId,
            'type' => $type,
            'points' => $points,
            'amount_paid' => $amountPaid,
            'payment_id' => $paymentId,
            'description' => $description,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
        ]);
    }

    /**
     * Deduct points from wallet
     */
    public function deductPoints(float $points, string $type = 'usage', $bookingId = null, $description = null): PointsTransaction
    {
        if ($this->balance < $points) {
            throw new \Exception('Insufficient points balance');
        }

        $balanceBefore = $this->balance;
        $this->decrement('balance', $points);
        $this->refresh();

        return PointsTransaction::create([
            'user_id' => $this->user_id,
            'wallet_id' => $this->id,
            'booking_id' => $bookingId,
            'type' => $type,
            'points' => -$points, // Negative for deduction
            'description' => $description,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
        ]);
    }
}
