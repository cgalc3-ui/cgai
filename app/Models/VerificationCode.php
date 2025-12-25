<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VerificationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'code',
        'type',
        'attempts',
        'verified',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Generate a new verification code
     */
    public static function generate(string $phone, string $type = 'registration'): self
    {
        // Invalidate previous codes for this phone and type
        self::where('phone', $phone)
            ->where('type', $type)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->update(['verified' => true]); // Mark as used

        // Generate new code (fixed for testing)
        // TODO: Remove this in production - use random code
        $code = '123456';
        
        // Original code generation (uncomment for production):
        // $codeLength = config('sms.verification.code_length', 6);
        // $code = str_pad(rand(0, pow(10, $codeLength) - 1), $codeLength, '0', STR_PAD_LEFT);
        
        $expiresIn = config('sms.verification.expires_in_minutes', 10);

        return self::create([
            'phone' => $phone,
            'code' => $code,
            'type' => $type,
            'attempts' => 0,
            'verified' => false,
            'expires_at' => now()->addMinutes($expiresIn),
        ]);
    }

    /**
     * Verify a code
     */
    public static function verify(string $phone, string $code, string $type = 'registration'): bool
    {
        $verificationCode = self::where('phone', $phone)
            ->where('code', $code)
            ->where('type', $type)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verificationCode) {
            return false;
        }

        // Check max attempts
        if ($verificationCode->attempts >= config('sms.verification.max_attempts', 5)) {
            return false;
        }

        // Increment attempts
        $verificationCode->increment('attempts');

        // If code matches, mark as verified
        if ($verificationCode->code === $code) {
            $verificationCode->update([
                'verified' => true,
                'verified_at' => now(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Check if code is valid (not expired and not verified)
     */
    public function isValid(): bool
    {
        return !$this->verified && 
               $this->expires_at > now() &&
               $this->attempts < config('sms.verification.max_attempts', 5);
    }

    /**
     * Get active code for phone and type
     */
    public static function getActive(string $phone, string $type = 'registration'): ?self
    {
        return self::where('phone', $phone)
            ->where('type', $type)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
