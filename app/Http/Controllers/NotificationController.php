<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications for authenticated user
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $perPage = $request->get('per_page', 20);
        $onlyUnread = $request->boolean('unread');

        $query = Notification::forUser($userId)
            ->orderBy('created_at', 'desc');

        if ($onlyUnread) {
            $query->unread();
        }

        $notifications = $query->paginate($perPage);

        // Add computed properties
        $notifications->getCollection()->transform(function ($notification) {
            return [
                'idNotification' => $notification->idNotification,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'metadata' => $notification->metadata,
                'priority' => $notification->priority,
                'is_read' => $notification->is_read,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'time_ago' => $notification->getTimeAgo(),
                'icon' => $notification->getIcon(),
                'color' => $notification->getColor(),
                'background_color' => $notification->getBackgroundColor()
            ];
        });

        return response()->json($notifications);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $userId = Auth::id();
        $count = Notification::forUser($userId)->unread()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (last 10 unread)
     */
    public function recent()
    {
        $userId = Auth::id();
        $notifications = Notification::forUser($userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $notifications->transform(function ($notification) {
            return [
                'idNotification' => $notification->idNotification,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'metadata' => $notification->metadata,
                'priority' => $notification->priority,
                'created_at' => $notification->created_at,
                'time_ago' => $notification->getTimeAgo(),
                'icon' => $notification->getIcon(),
                'color' => $notification->getColor()
            ];
        });

        return response()->json($notifications);
    }

    /**
     * Get notification statistics
     */
    public function stats()
    {
        $userId = Auth::id();
        $stats = $this->notificationService->getUserStats($userId);

        return response()->json($stats);
    }

    /**
     * Show specific notification
     */
    public function show($id)
    {
        $userId = Auth::id();
        $notification = Notification::forUser($userId)->findOrFail($id);

        return response()->json([
            'idNotification' => $notification->idNotification,
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
            'metadata' => $notification->metadata,
            'priority' => $notification->priority,
            'is_read' => $notification->is_read,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
            'time_ago' => $notification->getTimeAgo(),
            'icon' => $notification->getIcon(),
            'color' => $notification->getColor(),
            'background_color' => $notification->getBackgroundColor()
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $userId = Auth::id();
        $notification = Notification::forUser($userId)->findOrFail($id);
        
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread($id)
    {
        $userId = Auth::id();
        $notification = Notification::forUser($userId)->findOrFail($id);
        
        $notification->markAsUnread();

        return response()->json([
            'message' => 'Notification marked as unread',
            'notification' => $notification
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = Auth::id();
        
        Notification::forUser($userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $userId = Auth::id();
        $notification = Notification::forUser($userId)->findOrFail($id);
        
        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead()
    {
        $userId = Auth::id();
        
        Notification::forUser($userId)
            ->read()
            ->delete();

        return response()->json(['message' => 'All read notifications deleted']);
    }

    /**
     * Send test notification (admin only)
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'sometimes|in:low,medium,high,critical'
        ]);

        $userId = Auth::id();
        
        $notification = $this->notificationService->createNotification(
            $userId,
            'system_announcement',
            $request->title,
            $request->message,
            ['test' => true],
            $request->get('priority', 'medium')
        );

        return response()->json([
            'message' => 'Test notification sent',
            'notification' => $notification
        ], 201);
    }
}
