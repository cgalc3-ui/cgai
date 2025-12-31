<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Send notification to a user
     * 
     * @param User $user
     * @param string $type
     * @param string $title Translation key or direct text
     * @param string $message Translation key or direct text
     * @param array $data Additional data for translation placeholders
     */
    public function send(User $user, string $type, string $title, string $message, array $data = [])
    {
        // Store translation keys directly, not translated text
        // The Notification model will handle translation on display
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title, // This should be a translation key like 'messages.booking_created_successfully'
            'message' => $message, // This should be a translation key like 'messages.new_booking_created_for_service'
            'data' => $data,
            'read' => false,
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMany(iterable $users, string $type, string $title, string $message, array $data = [])
    {
        $notifications = [];
        
        foreach ($users as $user) {
            $notifications[] = $this->send($user, $type, $title, $message, $data);
        }

        return $notifications;
    }

    /**
     * Notify admin users
     */
    public function notifyAdmins(string $type, string $title, string $message, array $data = [])
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();
        return $this->sendToMany($admins, $type, $title, $message, $data);
    }

    /**
     * Notify staff users
     */
    public function notifyStaff(string $type, string $title, string $message, array $data = [])
    {
        $staff = User::where('role', User::ROLE_STAFF)->get();
        return $this->sendToMany($staff, $type, $title, $message, $data);
    }

    /**
     * Notify customer
     */
    public function notifyCustomer(User $customer, string $type, string $title, string $message, array $data = [])
    {
        if (!$customer->isCustomer()) {
            throw new \InvalidArgumentException('User must be a customer');
        }

        return $this->send($customer, $type, $title, $message, $data);
    }

    /**
     * Booking created notification
     */
    public function bookingCreated($booking)
    {
        $serviceName = $booking->service ? $booking->service->name : ($booking->consultation ? $booking->consultation->name : '');
        
        // Notify customer - Store translation keys, not translated text
        $this->notifyCustomer(
            $booking->customer,
            'booking_created',
            'messages.booking_created_successfully',
            'messages.new_booking_created_for_service',
            [
                'booking_id' => $booking->id,
                'service_id' => $booking->service_id,
                'consultation_id' => $booking->consultation_id,
                'booking_date' => $booking->booking_date,
                'service' => $serviceName, // Store service name in data for translation
            ]
        );

        // Notify employee
        if ($booking->employee && $booking->employee->user) {
            $this->send(
                $booking->employee->user,
                'booking_assigned',
                'messages.new_booking_assigned_to_you',
                'messages.new_booking_assigned_for_service',
                [
                    'booking_id' => $booking->id,
                    'service_id' => $booking->service_id,
                    'consultation_id' => $booking->consultation_id,
                    'customer_id' => $booking->customer_id,
                    'booking_date' => $booking->booking_date,
                    'service' => $serviceName, // Store service name in data for translation
                ]
            );
        }

        // Notify admins
        $this->notifyAdmins(
            'new_booking',
            'messages.new_booking',
            'messages.new_booking_created_by_customer',
            [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'service_id' => $booking->service_id,
                'consultation_id' => $booking->consultation_id,
                'customer' => $booking->customer->name, // Store customer name in data for translation
            ]
        );
    }

    /**
     * Booking status updated notification
     */
    public function bookingStatusUpdated($booking, $oldStatus)
    {
        $statusKeys = [
            'confirmed' => 'messages.booking_confirmed',
            'cancelled' => 'messages.booking_cancelled',
            'completed' => 'messages.booking_completed',
            'in_progress' => 'messages.booking_started',
        ];

        $titleKey = $statusKeys[$booking->status] ?? 'messages.booking_status_updated';
        
        $serviceName = $booking->service ? $booking->service->name : ($booking->consultation ? $booking->consultation->name : '');

        // Notify customer - Store translation keys, not translated text
        $this->notifyCustomer(
            $booking->customer,
            'booking_status_updated',
            $titleKey,
            'messages.booking_status_updated_for_service',
            [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $booking->status,
                'service' => $serviceName, // Store service name in data for translation
                'status_key' => $titleKey, // Store status key for translation
            ]
        );

        // Notify employee
        if ($booking->employee && $booking->employee->user) {
            $this->send(
                $booking->employee->user,
                'booking_status_updated',
                $titleKey,
                'messages.booking_status_updated_for_service_employee',
                [
                    'booking_id' => $booking->id,
                    'old_status' => $oldStatus,
                    'new_status' => $booking->status,
                    'service' => $serviceName, // Store service name in data for translation
                ]
            );
        }
    }

    /**
     * Payment received notification
     */
    public function paymentReceived($booking)
    {
        // Notify customer - Store translation keys, not translated text
        $this->notifyCustomer(
            $booking->customer,
            'payment_received',
            'messages.payment_received',
            'messages.payment_received_successfully_for_booking',
            [
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
            ]
        );

        // Notify admins
        $this->notifyAdmins(
            'payment_received',
            'messages.payment_received_admin',
            'messages.payment_received_from_customer',
            [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'amount' => $booking->total_price,
                'customer' => $booking->customer->name, // Store customer name in data for translation
            ]
        );
    }
}

