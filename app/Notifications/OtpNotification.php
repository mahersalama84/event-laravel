<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;
    protected $OTP;

    public function __construct($otp)
    {
        $this->OTP = $otp;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hi!')
            ->subject('Eveky, your code')
            ->line('Your code is: ')
            ->line($this->OTP)
            // ->action('Notification Action', url('/'))
            ->line('Thank you for using Eveky!');
    }
}
