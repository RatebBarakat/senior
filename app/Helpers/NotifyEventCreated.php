<?php
namespace App\Helpers;

use App\Jobs\SendPdfByEmail;
use App\Models\Appointment;
use App\Models\Event;
use App\Models\User;
use App\Notifications\notifyEventCreated as NotificationsNotifyEventCreated;
use Illuminate\Support\Facades\Notification;

class NotifyEventCreated 
{
    public static function notifyUsers(Event $event)
    {
        $users = User::get();
        Notification::send($users, (new NotificationsNotifyEventCreated($event)));
    }
}
