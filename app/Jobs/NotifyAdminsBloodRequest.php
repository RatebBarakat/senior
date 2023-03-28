<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\BloodRequest;
use App\Notifications\BloodRequestNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsBloodRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected BloodRequest $bloodRequest;

    /**
     * Create a new job instance.
     */
    public function __construct(BloodRequest $bloodRequest)
    {
        $this->bloodRequest = $bloodRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $admins = Admin::whereHas('role', function($query) {
            $query->where('name', 'center-employee');
        })->get();
        

        Notification::send($admins, new BloodRequestNotification($this->bloodRequest));
    }
}
