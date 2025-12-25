<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\Sms\FourJawalySmsService;

class FourJawalySmsChannel
{
    protected $smsService;

    public function __construct(FourJawalySmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFourJawalySms')) {
            throw new \InvalidArgumentException(
                'Notification must implement toFourJawalySms method'
            );
        }

        $message = $notification->toFourJawalySms($notifiable);
        
        if (!$message) {
            return;
        }

        // Get phone number from notifiable
        $phone = $this->getPhoneNumber($notifiable);
        
        if (!$phone) {
            throw new \InvalidArgumentException(
                'Notifiable must have a phone number'
            );
        }

        // Get metadata from notification
        $metadata = [
            'event_type' => $notification->eventType ?? 'notification',
            'entity_type' => $notification->entityType ?? null,
            'entity_id' => $notification->entityId ?? null,
        ];

        // Send SMS
        $result = $this->smsService->sendSMS($phone, $message, $metadata);

        // If SMS failed and notification implements onSmsFailed, call it
        if (!$result['success'] && method_exists($notification, 'onSmsFailed')) {
            $notification->onSmsFailed($notifiable, $result);
        }

        return $result;
    }

    /**
     * Get phone number from notifiable
     */
    protected function getPhoneNumber($notifiable): ?string
    {
        // Try different phone field names
        $phoneFields = ['phone', 'phone_number', 'mobile', 'mobile_number'];
        
        foreach ($phoneFields as $field) {
            if (isset($notifiable->$field) && !empty($notifiable->$field)) {
                return $notifiable->$field;
            }
        }

        return null;
    }
}

