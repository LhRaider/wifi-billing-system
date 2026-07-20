<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function create(
        string $title,
        string $message,
        string $type = 'info',
        string $audience = 'admin',
        ?int $userId = null
    ): void {
        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'audience' => $audience,
            'is_read' => false,
        ]);
    }
}