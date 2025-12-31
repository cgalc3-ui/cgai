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

        return response()->json([
            'success' => true,
            'data' => $notifications,
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

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => __('messages.notification_marked_read'),
            'data' => $notification->fresh(),
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
}
