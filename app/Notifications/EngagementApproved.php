<?php

namespace App\Notifications;

use App\Models\Engagement;
use App\Notifications\Channels\LogSmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EngagementApproved extends Notification
{
    public function __construct(public Engagement $engagement)
    {
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', LogSmsChannel::class];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your architect has approved your request - Architecture263')
            ->greeting('Hello '.$notifiable->name.',')
            ->line($this->engagement->architect->name.' has approved your engagement request.')
            ->line('You can now purchase the Blue Book to proceed.');
    }

    public function toSms(mixed $notifiable): string
    {
        return 'Architecture263: Your architect approved your engagement request.';
    }
}
