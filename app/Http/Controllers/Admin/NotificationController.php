<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display notifications for the current account.
     */
    public function index()
    {
        $user = auth()->user();

        $this->ensureAllowedRole($user);

        if ($user->role === 'super_admin') {

            /*
            |--------------------------------------------------------------------------
            | Super Admin Notifications
            |--------------------------------------------------------------------------
            |
            | Super Admin only sees platform-level notifications.
            |
            */

            $notifications = Notification::where(
                'audience',
                'super_admin'
            )
                ->latest()
                ->paginate(15);

        } else {

            /*
            |--------------------------------------------------------------------------
            | ISP Admin Notifications
            |--------------------------------------------------------------------------
            |
            | An ISP Admin may only see notifications explicitly assigned
            | to their own user account.
            |
            */

            $notifications = Notification::where(
                'audience',
                'admin'
            )
                ->where(
                    'user_id',
                    $user->id
                )
                ->latest()
                ->paginate(15);
        }

        return view(
            'admin.notifications.index',
            compact('notifications')
        );
    }

    /**
     * Mark one notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $user = auth()->user();

        $this->ensureAllowedRole($user);

        if ($user->role === 'super_admin') {

            /*
            |--------------------------------------------------------------------------
            | Super Admin Ownership Check
            |--------------------------------------------------------------------------
            */

            if (
                $notification->audience !== 'super_admin'
            ) {
                abort(
                    403,
                    'You are not authorized to access this notification.'
                );
            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | ISP Admin Ownership Check
            |--------------------------------------------------------------------------
            |
            | Both audience AND user_id must match.
            |
            */

            if (
                $notification->audience !== 'admin'
                ||
                (int) $notification->user_id !== (int) $user->id
            ) {
                abort(
                    403,
                    'You are not authorized to access this notification.'
                );
            }
        }

        if (!$notification->is_read) {

            $notification->update([
                'is_read' => true,
            ]);
        }

        return back()->with(
            'success',
            'Notification marked as read.'
        );
    }

    /**
     * Mark all notifications belonging to the current account as read.
     */
    public function markAllAsRead()
    {
        $user = auth()->user();

        $this->ensureAllowedRole($user);

        if ($user->role === 'super_admin') {

            /*
            |--------------------------------------------------------------------------
            | Super Admin Notifications Only
            |--------------------------------------------------------------------------
            */

            Notification::where(
                'audience',
                'super_admin'
            )
                ->where(
                    'is_read',
                    false
                )
                ->update([
                    'is_read' => true,
                ]);

        } else {

            /*
            |--------------------------------------------------------------------------
            | Current ISP Admin Only
            |--------------------------------------------------------------------------
            |
            | This prevents one ISP Admin from marking another ISP Admin's
            | notifications as read.
            |
            */

            Notification::where(
                'audience',
                'admin'
            )
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'is_read',
                    false
                )
                ->update([
                    'is_read' => true,
                ]);
        }

        return back()->with(
            'success',
            'All notifications marked as read.'
        );
    }

    /**
     * Defense-in-depth role validation.
     */
    private function ensureAllowedRole($user): void
    {
        if (
            !$user
            ||
            !in_array(
                $user->role,
                [
                    'admin',
                    'super_admin',
                ],
                true
            )
        ) {
            abort(
                403,
                'You are not authorized to access notifications.'
            );
        }
    }
}