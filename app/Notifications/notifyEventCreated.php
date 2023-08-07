<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class notifyEventCreated extends Notification  implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
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
        $centers = implode(',', $this->event->centers()->get()->pluck('name')->toArray());
        $message = trim(preg_replace('/\s+/', ' ', "new event was created at {$centers} from
                                    {$this->event->start_date} to {$this->event->end_date}"));
        return (new MailMessage)
            ->line($message)
            ->action('click here to see more details', "http://localhost:8080/event/{$this->event->id}")
            ->line('Thank you for using our application!');
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $centers = implode(',', $this->event->centers()->get()->pluck('name')->toArray());
        return [
            'message' => "new event was created at {$centers} from {$this->event->start_date}
                     to {$this->event->end_date}, check you email for more data",
            'url' => "/event/{$this->event->id}",
        ];
    }
}
