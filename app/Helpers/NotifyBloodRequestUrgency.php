<?php

namespace App\Helpers;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\BloodRequest;
use App\Models\Event;
use App\Models\User;
use App\Notifications\notifyBloodRequestUrgent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;

class NotifyBloodRequestUrgency 
{
    public static function notifyUsers(BloodRequest $bloodRequest)
    {
        $users = User::whereDoesntHave('appointments')
            ->orWhereHas('appointments', function (Builder $query) {
                $query->latest('id')->limit(1)->where('date', '<', Carbon::now()->subMonths(2));
            })
            ->get();

        $admins = Admin::whereDoesntHave('appointments')
            ->orWhereHas('appointments', function (Builder $query) {
                $query->latest('id')->limit(1)->where('date', '<', Carbon::now()->subMonths(2));
            })
            ->get();

        $actors = $users->merge($admins);

        Notification::send($users, new notifyBloodRequestUrgent($bloodRequest));
        Notification::send($admins, new notifyBloodRequestUrgent($bloodRequest));
    }
}
