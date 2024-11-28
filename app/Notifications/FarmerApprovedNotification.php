<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class FarmerApprovedNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Farmer Registration Approved')
            ->line('Congratulations! Your farmer account has been approved by the admin.')
            ->line('You can now access all farmer features in the system.');
    }
}
