<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
   
    
       public function index()
{
    if (auth()->user()->role === 'super_admin') {
        $notifications = Notification::where('audience', 'super_admin')
            ->latest()
            ->paginate(15);
    } else {
        $notifications = Notification::where('audience', 'admin')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
    }

    return view('admin.notifications.index', compact('notifications'));
}


    public function markAsRead(Notification $notification)
    {
        $audience = auth()->user()->role === 'super_admin'
            ? 'super_admin'
            : 'admin';

        if ($notification->audience !== $audience) {
            abort(403);
        }

        $notification->update([
            'is_read' => true,
        ]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $audience = auth()->user()->role === 'super_admin'
            ? 'super_admin'
            : 'admin';

        Notification::where('audience', $audience)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return back()->with('success', 'All notifications marked as read.');
    }
}