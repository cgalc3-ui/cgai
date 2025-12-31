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
            $params = $this->getTranslationParams('title');
            $translated = __($title, $params);
            // If translation exists, return it, otherwise return original
            return ($translated !== $title) ? $translated : $title;
        }
        
        // Try to match old patterns (Arabic or English) and translate them
        $translated = $this->translateWithParams($title, $this->data ?? []);
        if ($translated !== $title && !empty($translated)) {
            return $translated;
        }
        
        // Return as is (for old notifications with direct text)
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
            $params = $this->getTranslationParams('message');
            $translated = __($message, $params);
            // If translation exists and is different from key, return it
            if ($translated !== $message && !empty($translated)) {
                return $translated;
            }
        }
        
        // Try to match old patterns (Arabic or English) and translate them
        $translated = $this->translateWithParams($message, $this->data ?? []);
        if ($translated !== $message && !empty($translated)) {
            return $translated;
        }
        
        // Return as is (for old notifications with direct text)
        return $message;
    }

    /**
     * Get translation parameters from data
     */
    protected function getTranslationParams($type)
    {
        if (!$this->data) {
            return [];
        }

        $params = [];
        
        // Extract service/consultation name
        if (isset($this->data['service_id'])) {
            $service = \App\Models\Service::find($this->data['service_id']);
            if ($service) {
                $params['service'] = $service->name;
            }
        }
        
        if (isset($this->data['consultation_id'])) {
            $consultation = \App\Models\Consultation::find($this->data['consultation_id']);
            if ($consultation) {
                $params['service'] = $consultation->name;
            }
        }
        
        // Extract customer name
        if (isset($this->data['customer_id'])) {
            $customer = \App\Models\User::find($this->data['customer_id']);
            if ($customer) {
                $params['customer'] = $customer->name;
            }
        }
        
        // Extract booking ID
        if (isset($this->data['booking_id'])) {
            $params['booking_id'] = $this->data['booking_id'];
        }
        
        // Extract status
        if (isset($this->data['status_key'])) {
            $params['status'] = __($this->data['status_key']);
        } elseif (isset($this->data['new_status'])) {
            $statusKey = 'messages.booking_' . $this->data['new_status'];
            $translated = __($statusKey);
            $params['status'] = ($translated !== $statusKey) ? $translated : $this->data['new_status'];
        }
        
        // Extract service name from data if stored directly
        if (isset($this->data['service'])) {
            $params['service'] = $this->data['service'];
        }
        
        // Extract customer name from data if stored directly
        if (isset($this->data['customer'])) {
            $params['customer'] = $this->data['customer'];
        }
        
        // Extract package name from data if stored directly
        if (isset($this->data['package'])) {
            $params['package'] = $this->data['package'];
        }
        
        // Extract user name from data if stored directly
        if (isset($this->data['user'])) {
            $params['user'] = $this->data['user'];
        }
        
        // Extract subject from data if stored directly
        if (isset($this->data['subject'])) {
            $params['subject'] = $this->data['subject'];
        }
        
        // Extract expires_in from data if stored directly
        if (isset($this->data['expires_in'])) {
            $params['expires_in'] = $this->data['expires_in'];
        }
        
        // Extract name from data if stored directly (for subscriptions)
        if (isset($this->data['name'])) {
            $params['name'] = $this->data['name'];
        }
        
        return $params;
    }

    /**
     * Translate with parameters
     */
    protected function translateWithParams($text, $data)
    {
        // Try to find translation key pattern for old Arabic and English notifications
        $patterns = [
            // Arabic patterns
            '/طلب اشتراك جديد/' => 'messages.new_subscription_request',
            '/طلب اشتراك جديد من المستخدم: (.+)/' => ['key' => 'messages.new_subscription_request_from_user', 'params' => ['user' => 1]],
            '/تم إنشاء باقة جديدة/' => 'messages.new_subscription_package_created',
            '/تم إنشاء باقة جديدة: (.+)/' => ['key' => 'messages.new_subscription_package_created_with_name', 'params' => ['name' => 1]],
            '/تم قبول طلب الاشتراك/' => 'messages.subscription_request_approved',
            '/تم قبول طلب الاشتراك في باقة: (.+)/' => ['key' => 'messages.subscription_request_approved_for_package', 'params' => ['package' => 1]],
            '/تم رفض طلب الاشتراك/' => 'messages.subscription_request_rejected',
            '/تم رفض طلب الاشتراك في باقة: (.+)\. السبب: (.+)/' => ['key' => 'messages.subscription_request_rejected_for_package', 'params' => ['package' => 1, 'reason' => 2]],
            '/تم إنشاء حجز جديد للخدمة: (.+)/' => ['key' => 'messages.new_booking_created_for_service', 'params' => ['service' => 1]],
            '/تم تعيين حجز جديد للخدمة: (.+)/' => ['key' => 'messages.new_booking_assigned_for_service', 'params' => ['service' => 1]],
            '/تم إنشاء حجز جديد من العميل: (.+)/' => ['key' => 'messages.new_booking_created_by_customer', 'params' => ['customer' => 1]],
            '/تم تحديث حالة حجزك للخدمة: (.+) إلى: (.+)/' => ['key' => 'messages.booking_status_updated_for_service', 'params' => ['service' => 1, 'status' => 2]],
            '/تم تحديث حالة الحجز للخدمة: (.+)/' => ['key' => 'messages.booking_status_updated_for_service_employee', 'params' => ['service' => 1]],
            '/تم استلام الدفع بنجاح للحجز رقم: (\d+)/' => ['key' => 'messages.payment_received_successfully_for_booking', 'params' => ['booking_id' => 1]],
            '/تم استلام دفعة من العميل: (.+)/' => ['key' => 'messages.payment_received_from_customer', 'params' => ['customer' => 1]],
            '/حجز جديد/' => 'messages.new_booking',
            '/تم استلام دفعة/' => 'messages.payment_received_admin',
            '/اشتراكك على وشك الانتهاء/' => 'messages.subscription_expiring_soon',
            '/اشتراكك في باقة (.+) سينتهي في (.+)/' => ['key' => 'messages.subscription_expiring_for_package', 'params' => ['package' => 1, 'expires_in' => 2]],
            '/تذكرة دعم جديدة/' => 'messages.new_support_ticket',
            '/تم فتح تذكرة دعم جديدة من المستخدم: (.+) - الموضوع: (.+)/' => ['key' => 'messages.new_support_ticket_from_user', 'params' => ['user' => 1, 'subject' => 2]],
            // English patterns (for old notifications)
            '/^New Subscription Request$/i' => 'messages.new_subscription_request',
            '/New Subscription Request from user: (.+)/i' => ['key' => 'messages.new_subscription_request_from_user', 'params' => ['user' => 1]],
            '/^New Subscription Package Created$/i' => 'messages.new_subscription_package_created',
            '/New subscription package created: (.+)/i' => ['key' => 'messages.new_subscription_package_created_with_name', 'params' => ['name' => 1]],
            '/^New Booking$/i' => 'messages.new_booking',
            '/New booking created by customer: (.+)/i' => ['key' => 'messages.new_booking_created_by_customer', 'params' => ['customer' => 1]],
            '/^Payment Received$/i' => 'messages.payment_received_admin',
            '/Payment received from customer: (.+)/i' => ['key' => 'messages.payment_received_from_customer', 'params' => ['customer' => 1]],
        ];
        
        foreach ($patterns as $pattern => $keyConfig) {
            if (preg_match($pattern, $text, $matches)) {
                $key = is_array($keyConfig) ? $keyConfig['key'] : $keyConfig;
                $params = [];
                
                if (is_array($keyConfig) && isset($keyConfig['params'])) {
                    foreach ($keyConfig['params'] as $paramName => $matchIndex) {
                        if (isset($matches[$matchIndex])) {
                            $params[$paramName] = $matches[$matchIndex];
                        }
                    }
                } else {
                    // Simple pattern matching
                    if (isset($matches[1])) {
                        if (is_numeric($matches[1])) {
                            $params['booking_id'] = $matches[1];
                        } else {
                            $params['service'] = $matches[1];
                        }
                    }
                    if (isset($matches[2])) {
                        $params['status'] = $matches[2];
                    }
                }
                
                $translated = __($key, $params);
                // Only return if translation is different (meaning it was found)
                if ($translated !== $key) {
                    return $translated;
                }
            }
        }
        
        return $text;
    }
}
