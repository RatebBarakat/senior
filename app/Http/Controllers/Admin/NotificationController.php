<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function markAsRead(string $notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        if (!$notification) {
            return response()->json('notification not fund',403);
        }
        if ($notification->notifiable_id != auth()->guard('admin')->user()->id) {
            return response()->json('unauthenrized',403);
        }

        $notification->markAsRead();
        return response()->json(request()->input('url'));

    }

}
