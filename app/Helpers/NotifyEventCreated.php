<?php
namespace App\Helpers;

use App\Jobs\SendPdfByEmail;
use App\Models\Appointment;
use App\Models\Event;
use App\Models\User;
use App\Notifications\notifyEventCreated as NotificationsNotifyEventCreated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;

class NotifyEventCreated 
{
    public static function notifyUsers(Event $event)
    {
        $users = User::whereDoesntHave('appointments')
                ->orWhereHas('appointments', function (Builder $query) {
                    $query->latest('id')->limit(1)->where('date', '<', Carbon::now()->subMonths(2));
                })
                ->get();
        Notification::send($users, (new NotificationsNotifyEventCreated($event)));
    }
}
