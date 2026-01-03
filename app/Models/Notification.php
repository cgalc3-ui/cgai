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
        // Check if title is a translation key (starts with 'messages.')
        if (strpos($this->title, 'messages.') === 0) {
            $translated = __($this->title);
            
            // If translation exists and has placeholders, replace them with data
            if ($this->data && is_array($this->data)) {
                $translated = $this->replacePlaceholders($translated);
            }
            
            return $translated;
        }
        
        // If not a translation key, return as is
        return $this->title;
    }

    /**
     * Get translated message
     */
    public function getTranslatedMessageAttribute()
    {
        // Check if message is a translation key (starts with 'messages.')
        if (strpos($this->message, 'messages.') === 0) {
            $translated = __($this->message);
            
            // If translation exists and has placeholders, replace them with data
            if ($this->data && is_array($this->data)) {
                $translated = $this->replacePlaceholders($translated);
            }
            
            return $translated;
        }
        
        // If not a translation key, return as is
        return $this->message;
    }

    /**
     * Replace placeholders in translated text with data values
     */
    private function replacePlaceholders($text)
    {
        if (!is_array($this->data)) {
            return $text;
        }

        // Replace service name
        if (isset($this->data['service'])) {
            $text = str_replace(':service', $this->data['service'], $text);
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
            $text = str_replace(':package', $this->data['package'], $text);
        }

        // Replace reason
        if (isset($this->data['reason'])) {
            $text = str_replace(':reason', $this->data['reason'], $text);
        }

        // Replace expires_in
        if (isset($this->data['expires_in'])) {
            $text = str_replace(':expires_in', $this->data['expires_in'], $text);
        }

        return $text;
    }
}
