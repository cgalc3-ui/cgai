<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'entity_type',
        'entity_id',
        'phone',
        'message',
        'status',
        'provider',
        'provider_message_id',
        'provider_response',
        'error_message',
        'attempt',
        'hash',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'provider_response' => 'array',
    ];

    /**
     * Create a new SMS log entry
     */
    public static function createLog(array $data): self
    {
        return self::create(array_merge([
            'status' => 'pending',
            'provider' => 'fourjawaly',
            'attempt' => 1,
            'hash' => self::generateHash($data),
        ], $data));
    }

    /**
     * Generate unique hash for idempotency
     */
    public static function generateHash(array $data): string
    {
        $hashData = [
            $data['event_type'] ?? '',
            $data['entity_type'] ?? '',
            $data['entity_id'] ?? '',
            $data['phone'] ?? '',
            $data['message'] ?? '',
            now()->format('Y-m-d H:i:s'),
        ];

        return md5(implode('|', $hashData));
    }

    /**
     * Mark SMS as sent
     */
    public function markAsSent(string $providerMessageId = null, array $providerResponse = null): void
    {
        $this->update([
            'status' => 'sent',
            'provider_message_id' => $providerMessageId,
            'provider_response' => $providerResponse,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark SMS as failed
     */
    public function markAsFailed(string $errorMessage, array $providerResponse = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'provider_response' => $providerResponse,
        ]);
    }

    /**
     * Mark SMS as delivered
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Increment attempt count
     */
    public function incrementAttempt(): void
    {
        $this->increment('attempt');
    }

    /**
     * Check if SMS can be retried
     */
    public function canRetry(): bool
    {
        return $this->attempt < config('sms.retry.max_attempts', 3) && 
               $this->status === 'failed';
    }

    /**
     * Scope for failed SMS logs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for pending SMS logs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for sent SMS logs
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Get masked phone number for privacy
     */
    public function getMaskedPhoneAttribute(): string
    {
        if (!config('sms.logging.mask_phone', true)) {
            return $this->phone;
        }

        $phone = $this->phone;
        if (strlen($phone) > 4) {
            return substr($phone, 0, 3) . str_repeat('*', strlen($phone) - 6) . substr($phone, -3);
        }

        return str_repeat('*', strlen($phone));
    }
}
