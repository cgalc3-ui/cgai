<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Send notification to a user
     */
    public function send(User $user, string $type, string $title, string $message, array $data = [])
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
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
        // Notify customer
        $this->notifyCustomer(
            $booking->customer,
            'booking_created',
            'تم إنشاء الحجز بنجاح',
            "تم إنشاء حجز جديد للخدمة: {$booking->service->name}",
            [
                'booking_id' => $booking->id,
                'service_id' => $booking->service_id,
                'booking_date' => $booking->booking_date,
            ]
        );

        // Notify employee
        if ($booking->employee && $booking->employee->user) {
            $this->send(
                $booking->employee->user,
                'booking_assigned',
                'تم تعيين حجز جديد لك',
                "تم تعيين حجز جديد للخدمة: {$booking->service->name}",
                [
                    'booking_id' => $booking->id,
                    'service_id' => $booking->service_id,
                    'customer_id' => $booking->customer_id,
                    'booking_date' => $booking->booking_date,
                ]
            );
        }

        // Notify admins
        $this->notifyAdmins(
            'new_booking',
            'حجز جديد',
            "تم إنشاء حجز جديد من العميل: {$booking->customer->name}",
            [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'service_id' => $booking->service_id,
            ]
        );
    }

    /**
     * Booking status updated notification
     */
    public function bookingStatusUpdated($booking, $oldStatus)
    {
        $statusMessages = [
            'confirmed' => 'تم تأكيد الحجز',
            'cancelled' => 'تم إلغاء الحجز',
            'completed' => 'تم إكمال الحجز',
            'in_progress' => 'تم بدء الحجز',
        ];

        $message = $statusMessages[$booking->status] ?? 'تم تحديث حالة الحجز';

        // Notify customer
        $this->notifyCustomer(
            $booking->customer,
            'booking_status_updated',
            $message,
            "تم تحديث حالة حجزك للخدمة: {$booking->service->name} إلى: {$message}",
            [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $booking->status,
            ]
        );

        // Notify employee
        if ($booking->employee && $booking->employee->user) {
            $this->send(
                $booking->employee->user,
                'booking_status_updated',
                $message,
                "تم تحديث حالة الحجز للخدمة: {$booking->service->name}",
                [
                    'booking_id' => $booking->id,
                    'old_status' => $oldStatus,
                    'new_status' => $booking->status,
                ]
            );
        }
    }

    /**
     * Payment received notification
     */
    public function paymentReceived($booking)
    {
        // Notify customer
        $this->notifyCustomer(
            $booking->customer,
            'payment_received',
            'تم استلام الدفع',
            "تم استلام الدفع بنجاح للحجز رقم: {$booking->id}",
            [
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
            ]
        );

        // Notify admins
        $this->notifyAdmins(
            'payment_received',
            'تم استلام دفعة',
            "تم استلام دفعة من العميل: {$booking->customer->name}",
            [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'amount' => $booking->total_price,
            ]
        );
    }
}

