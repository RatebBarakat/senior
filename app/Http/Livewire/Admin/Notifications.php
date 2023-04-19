<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class Notifications extends Component
{
    public function render()
    {
        $notifications = auth()->guard('admin')->user()->unreadNotifications;
        return view('livewire.admin.notifications',compact('notifications'));
    }
}
