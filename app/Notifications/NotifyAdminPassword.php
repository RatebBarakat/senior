<?php

namespace App\Notifications;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyAdminPassword extends Notification
{
    use Queueable;

    private Admin $admin;
    private string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(Admin $admin,string $token)
    {
        $this->admin = $admin;
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
        $role = $this->admin->role ? $this->admin->role->name : 'admin';
        return (new MailMessage)
                    ->line("set your account password as {$role}")
                    ->action('click here ', route('admin.setPassword',[$this->admin->id,$this->token]))
                    ->line('Thank you for using our application!');
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
