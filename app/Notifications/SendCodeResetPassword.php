<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class SendCodeResetPassword extends Notification
{
    use Queueable;

    protected $notifiable;
    protected $token;
    /**
     * Create a new notification instance.
     */
    public function __construct($notifiable,$token)
    {
        $this->notifiable = $notifiable;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->getUrl();
        return (new MailMessage)
                    ->line('reset your password')
                    ->action('Notification Action', $url)
                    ->line('Thank you for using our application!');
    }

    private function getUrl(){
        $expires = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));
        $email = $this->notifiable->getEmailForVerification();

        $url = route('changePassword.show', [$this->token]);
        return $url;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
