<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = auth()->user();

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

        $notifications = $query->paginate(10);
        $unreadCount = $user->unreadNotificationsCount();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $user = auth()->user();

        // Check if notification belongs to user
        if ($notification->user_id !== $user->id) {
            return back()->with('error', __('messages.notification_unauthorized_access'));
        }

        $notification->markAsRead();

        return back()->with('success', __('messages.notification_marked_read'));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = auth()->user();

        Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', __('messages.notification_all_marked_read'));
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        $user = auth()->user();

        // Check if notification belongs to user
        if ($notification->user_id !== $user->id) {
            return back()->with('error', __('messages.notification_cannot_delete'));
        }

        $notification->delete();

        return back()->with('success', __('messages.notification_deleted_success'));
    }
}
