<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Get all notifications
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->notifications
        );
    }

    // Get unread notifications only
    public function unread(Request $request)
    {
        return response()->json(
            $request->user()->unreadNotifications
        );
    }

    
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read'
        ]);
    }

    // Mark all notifications as read
    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }
}
