<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $query = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Filter by read status
        if ($request->has('read')) {
            $query->where('read', $request->boolean('read'));
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->paginate($request->get('per_page', 20));

        // Transform notifications to include translated title and message
        $transformedData = $notifications->getCollection()->map(function ($notification) use ($locale) {
            $data = $notification->data ?? [];
            
            // Translate data array based on locale
            $translatedData = $this->translateNotificationData($data, $locale);
            
            return [
                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type,
                'title' => $notification->translated_title,
                'message' => $notification->translated_message,
                'data' => $translatedData,
                'read' => $notification->read,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at,
            ];
        });

        // Create new paginator with transformed data
        $transformedNotifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $transformedData,
            $notifications->total(),
            $notifications->perPage(),
            $notifications->currentPage(),
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $transformedNotifications,
            'unread_count' => $user->unreadNotificationsCount(),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'count' => $user->unreadNotificationsCount(),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        $user = $request->user();

        // Check if notification belongs to user
        if ($notification->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('messages.notification_unauthorized_access'),
            ], 403);
        }

        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $notification->markAsRead();
        $notification = $notification->fresh();

        // Translate data array
        $translatedData = $this->translateNotificationData($notification->data ?? [], $locale);

        return response()->json([
            'success' => true,
            'message' => __('messages.notification_marked_read'),
            'data' => [
                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type,
                'title' => $notification->translated_title,
                'message' => $notification->translated_message,
                'data' => $translatedData,
                'read' => $notification->read,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at,
            ],
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.notification_all_marked_read'),
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, Notification $notification)
    {
        $user = $request->user();

        // Check if notification belongs to user
        if ($notification->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => __('messages.notification_cannot_delete'),
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.notification_deleted_success'),
        ]);
    }

    /**
     * Translate notification data based on locale
     */
    private function translateNotificationData($data, $locale)
    {
        if (!is_array($data)) {
            return $data;
        }

        $translated = $data;

        // Translate service name
        if (isset($data['service']) && isset($data['service_en'])) {
            $translated['service'] = ($locale === 'en' && !empty($data['service_en']))
                ? $data['service_en']
                : $data['service'];
        }

        // Translate package name
        if (isset($data['package']) && isset($data['package_en'])) {
            $translated['package'] = ($locale === 'en' && !empty($data['package_en']))
                ? $data['package_en']
                : $data['package'];
        }

        // Translate subscription name
        if (isset($data['name']) && isset($data['name_en'])) {
            $translated['name'] = ($locale === 'en' && !empty($data['name_en']))
                ? $data['name_en']
                : $data['name'];
        }

        // Translate subject (for tickets)
        if (isset($data['subject']) && isset($data['subject_en'])) {
            $translated['subject'] = ($locale === 'en' && !empty($data['subject_en']))
                ? $data['subject_en']
                : $data['subject'];
        }

        // Remove _en keys from response to keep it clean
        unset($translated['service_en']);
        unset($translated['package_en']);
        unset($translated['name_en']);
        unset($translated['subject_en']);

        return $translated;
    }
}
