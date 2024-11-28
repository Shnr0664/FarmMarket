<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class FarmerRejectedNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)->subject('Farmer Registration Rejected')
            ->greeting('Hello ' . $notifiable->personalInfo->name . '!')
            ->line('We regret to inform you that your farmer registration has been rejected by our admin team.')
            ->line('This decision might have been made because your application did not meet the necessary requirements.')
            ->line('If you believe this is an error or would like further clarification, please contact our support team.')
            ->action('Contact Support', url('/contact-support'))
            ->line('Thank you for your understanding.');
    }
}
