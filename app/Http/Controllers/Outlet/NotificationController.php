<?php

namespace App\Http\Controllers\Outlet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Mark single notification as read
    public function markAsRead($id)
    {
        $user = auth()->guard('outlet')->user();
        $outlet = $user->outlet;
        
        if ($outlet) {
            $notification = $outlet->notifications()->find($id);
            
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true]);
            }
        }
        
        return response()->json(['success' => false], 404);
    }

    // Mark all notifications as read
    public function markAllAsRead()
    {
        $user = auth()->guard('outlet')->user();
        $outlet = $user->outlet;
        
        if ($outlet) {
            $outlet->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }

    // Get unread count
    public function getUnreadCount()
    {
        $user = auth()->guard('outlet')->user();
        $outlet = $user->outlet;
        
        $count = $outlet ? $outlet->unreadNotifications()->count() : 0;
        
        return response()->json(['count' => $count]);
    }

    // Show all notifications page
    public function index()
    {
        $user = auth()->guard('outlet')->user();
        $outlet = $user->outlet;
        
        $notifications = $outlet ? $outlet->notifications()->paginate(20) : collect();
        
        return view('web.outlet.pages.notification-list',compact('notifications'));
    }
}