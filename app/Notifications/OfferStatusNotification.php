<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OfferStatusNotification extends Notification
{
    use Queueable;

    protected $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $status = ucfirst($this->offer->status);
        $message = (new MailMessage)
            ->subject('Your Offer has been ' . $status)
            ->line('Your offer for the product "' . $this->offer->product->product_name . '" has been ' . $status . '.');

        if ($this->offer->status == 'countered') {
            $message->line('Counter Offer Price: ' . $this->offer->counter_offer_price);
        }

        return $message;
    }
}