<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        if (!$this->read) {
            $this->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        if ($this->read) {
            $this->update([
                'read' => false,
                'read_at' => null,
            ]);
        }
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope to get notifications by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get translated title
     */
    public function getTranslatedTitleAttribute()
    {
        $title = $this->title;

        // Check if title is a translation key (starts with 'messages.')
        if (strpos($title, 'messages.') === 0) {
            $translated = __($title);

            // If translation exists and has placeholders, replace them with data
            if ($this->data && is_array($this->data)) {
                $translated = $this->replacePlaceholders($translated);
            }

            // If translation returns the same key, it means translation doesn't exist
            // Return the original title in that case
            if ($translated === $title) {
                return $title;
            }

            return $translated;
        }

        // If title doesn't start with 'messages.', it might be:
        // 1. Already translated text (old notifications) - return as is
        // 2. A translation key without prefix - try to translate it
        $translated = __('messages.' . $title);
        if ($translated !== 'messages.' . $title) {
            // Translation found
            if ($this->data && is_array($this->data)) {
                $translated = $this->replacePlaceholders($translated);
            }
            return $translated;
        }

        // If not a translation key, return as is (already translated or plain text)
        return $title;
    }

    /**
     * Get translated message
     */
    public function getTranslatedMessageAttribute()
    {
        $message = $this->message;

        // Check if message is a translation key (starts with 'messages.')
        if (strpos($message, 'messages.') === 0) {
            $translated = __($message);

            // If translation exists and has placeholders, replace them with data
            if ($this->data && is_array($this->data)) {
                $translated = $this->replacePlaceholders($translated);
            }

            // If translation returns the same key, it means translation doesn't exist
            // Return the original message in that case
            if ($translated === $message) {
                return $message;
            }

            return $translated;
        }

        // If message doesn't start with 'messages.', it might be:
        // 1. Already translated text (old notifications) - return as is
        // 2. A translation key without prefix - try to translate it
        $translated = __('messages.' . $message);
        if ($translated !== 'messages.' . $message) {
            // Translation found
            if ($this->data && is_array($this->data)) {
                $translated = $this->replacePlaceholders($translated);
            }
            return $translated;
        }

        // If not a translation key, return as is (already translated or plain text)
        return $message;
    }

    /**
     * Replace placeholders in translated text with data values
     */
    private function replacePlaceholders($text)
    {
        if (!is_array($this->data)) {
            return $text;
        }

        $locale = app()->getLocale();

        // Replace service name
        if (isset($this->data['service'])) {
            $service = ($locale === 'en' && !empty($this->data['service_en']))
                ? $this->data['service_en']
                : $this->data['service'];
            $text = str_replace(':service', $service, $text);
        }

        // Replace customer name
        if (isset($this->data['customer'])) {
            $text = str_replace(':customer', $this->data['customer'], $text);
        }

        // Replace amount
        if (isset($this->data['amount'])) {
            $amount = is_numeric($this->data['amount'])
                ? number_format($this->data['amount'], 2)
                : $this->data['amount'];
            $text = str_replace(':amount', $amount, $text);
        }

        // Replace booking date
        if (isset($this->data['booking_date'])) {
            $bookingDate = is_string($this->data['booking_date'])
                ? $this->data['booking_date']
                : (is_object($this->data['booking_date']) && method_exists($this->data['booking_date'], 'format')
                    ? $this->data['booking_date']->format('Y-m-d')
                    : $this->data['booking_date']);
            $text = str_replace(':date', $bookingDate, $text);
        }

        // Replace booking ID
        if (isset($this->data['booking_id'])) {
            $text = str_replace(':booking_id', $this->data['booking_id'], $text);
        }

        // Replace status (translate status if status_key exists)
        if (isset($this->data['status_key'])) {
            $statusText = __($this->data['status_key']);
            $text = str_replace(':status', $statusText, $text);
        } elseif (isset($this->data['new_status'])) {
            // Try to translate status directly
            $statusKey = 'messages.' . $this->data['new_status'];
            if (__($statusKey) !== $statusKey) {
                $text = str_replace(':status', __($statusKey), $text);
            } else {
                $text = str_replace(':status', $this->data['new_status'], $text);
            }
        }

        // Replace user name
        if (isset($this->data['user'])) {
            $text = str_replace(':user', $this->data['user'], $text);
        }

        // Replace package name
        if (isset($this->data['package'])) {
            $package = ($locale === 'en' && !empty($this->data['package_en']))
                ? $this->data['package_en']
                : $this->data['package'];
            $text = str_replace(':package', $package, $text);
        }

        // Replace name (for subscription name)
        if (isset($this->data['name'])) {
            $name = ($locale === 'en' && !empty($this->data['name_en']))
                ? $this->data['name_en']
                : $this->data['name'];
            $text = str_replace(':name', $name, $text);
        }

        // Replace subject (for support tickets)
        if (isset($this->data['subject'])) {
            $subject = ($locale === 'en' && !empty($this->data['subject_en']))
                ? $this->data['subject_en']
                : $this->data['subject'];
            $text = str_replace(':subject', $subject, $text);
        }

        // Replace reason
        if (isset($this->data['reason'])) {
            $text = str_replace(':reason', $this->data['reason'], $text);
        }

        // Replace expires_in
        if (isset($this->data['expires_in'])) {
            $expiresIn = $this->data['expires_in'];

            // If we have expires_at, recalculate it based on current locale
            if (isset($this->data['expires_at'])) {
                try {
                    $expiresAt = \Carbon\Carbon::parse($this->data['expires_at']);
                    $expiresIn = $expiresAt->diffForHumans();
                } catch (\Exception $e) {
                    // Fallback to stored expires_in
                }
            }

            $text = str_replace(':expires_in', $expiresIn, $text);
        }

        return $text;
    }
}
