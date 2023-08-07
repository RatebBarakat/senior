<?php

namespace App\Notifications;

use App\Models\Admin;
use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class BloodRequestCompleted extends Notification implements ShouldQueue
{
    use Queueable;
    
    public Admin $admin;
    public BloodRequest $bloodRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(Admin $admin,BloodRequest $bloodRequest)
    {
        $this->admin = $admin;
        $this->bloodRequest = $bloodRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        try {
            return (new MailMessage)
                    ->line('blood request comleted.')
                    ->action('Notification Action', "http://localhost:8080/blood-request")
                    ->line("your request was resolve by {$this->admin->name} at 
                    {$this->bloodRequest->center->name} center");
   
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }    
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "your request was resolve by {$this->admin->name} 
                         at {$this->bloodRequest->center->name} check you email for more data",
        ];
    }
}
