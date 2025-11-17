<?php

namespace App\Http\Controllers;

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
        
        $notifications = Notification::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(Request $request)
    {
        $user = $request->user();
        
        $count = Notification::forUser($user->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        
        $notification = Notification::forUser($user->id)->findOrFail($id);
        $notification->update(['read' => true]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        Notification::forUser($user->id)
            ->unread()
            ->update(['read' => true]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Delete a notification
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        $notification = Notification::forUser($user->id)->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Create a notification (for testing or admin use)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:alert,message,system,success,warning,maintenance',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'link' => 'nullable|string',
        ]);

        $notification = Notification::create($validated);

        return response()->json([
            'message' => 'Notification created',
            'notification' => $notification
        ], 201);
    }
}
