<?php

namespace App\Notifications;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    private Appointment $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $name = $this->appointment->user?->name ?? $this->appointment->admin?->name;

        return (new MailMessage)
            ->line("hello {$name}")
            ->line("thanks for donating {$this->appointment->quantity} of blood of type
                            {$this->appointment->blood_type} 
                            at {$this->appointment->center->name} center")
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $name = $this->appointment->user?->name ?? $this->appointment->admin?->name;
        $html = "hello {$name}\n";
        $html .= "thanks for donating {$this->appointment->quantity} of blood of type
         {$this->appointment->blood_type} 
        at {$this->appointment->center->name} center";

        return [
            'message' => $html
        ];
    }
}
