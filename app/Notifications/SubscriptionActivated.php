<?php

namespace App\Notifications;

use App\Notifications\Channels\LogSmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivated extends Notification
{
    public function __construct(public ?string $expiresAt = null)
    {
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', LogSmsChannel::class];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Blue Book access has been approved - Architecture263')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your Blue Book (ACZ Conditions of Engagement & Scale of Fees) purchase has been approved.')
            ->when($this->expiresAt, fn ($mail) => $mail->line('Access is valid until '.$this->expiresAt.'.'))
            ->line('You are now searchable by clients on the portal and can accept engagements.');
    }

    public function toSms(mixed $notifiable): string
    {
        return 'Architecture263: Your Blue Book access has been approved.';
    }
}
