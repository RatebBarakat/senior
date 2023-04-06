<?php

namespace App\Notifications;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BloodRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected BloodRequest $bloodRequest;
    /**
     * Create a new notification instance.
     */
    public function __construct(BloodRequest $bloodRequest)
    {
        $this->bloodRequest = $bloodRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'new blood request was generated click bellow to show it',
            'url' => $this->getActionUrl($notifiable),
        ];
    }
    
    protected function getActionUrl($notifiable)
    {
        return route('admin.blood-request.index', ['filter' => $this->bloodRequest->id]);
    }
}
