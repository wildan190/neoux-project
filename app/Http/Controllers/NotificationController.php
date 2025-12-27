<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['status' => 'success']);
        }

        $url = $this->sanitizeUrl($notification->data['url'] ?? route('notifications.index'));

        return redirect($url);
    }

    private function sanitizeUrl($url)
    {
        if (empty($url))
            return route('notifications.index');

        // If it starts with http://localhost:8000, replace with current APP_URL
        $localhost = 'http://localhost:8000';
        if (str_contains($url, $localhost)) {
            return str_replace($localhost, config('app.url'), $url);
        }

        return $url;
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->ajax()) {
            return response()->json(['status' => 'success']);
        }

        return back()->with('success', 'All notifications marked as read');
    }

    public function getUnreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications->count(),
        ]);
    }

    public function getLatestNotifications()
    {
        // Get last 5 notifications (mix of read and unread)
        $notifications = Auth::user()->notifications()->take(5)->get();

        return response()->json([
            'notifications' => $notifications,
        ]);
    }
}
