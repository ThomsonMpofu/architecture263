<?php

namespace App\Notifications;

use App\Notifications\Channels\LogSmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArchitectApproved extends Notification
{
    public function via(mixed $notifiable): array
    {
        return ['mail', LogSmsChannel::class];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You are now an approved architect - Architecture263')
            ->greeting('Congratulations, '.$notifiable->name.'!')
            ->line('The Institute of Architects of Zimbabwe has approved your registration as an architect.')
            ->line('You can now log in to the portal once your subscription is active.');
    }

    public function toSms(mixed $notifiable): string
    {
        return "Architecture263: Your architect registration has been approved.";
    }
}
