<?php

namespace App\Notifications;

use App\Models\PlanApplication;
use App\Notifications\Channels\LogSmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlanApplicationDecided extends Notification
{
    public function __construct(public PlanApplication $planApplication)
    {
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', LogSmsChannel::class];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $label = str_replace('_', ' ', $this->planApplication->status);

        return (new MailMessage)
            ->subject('Decision on plan '.$this->planApplication->plan_no.' - Architecture263')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Plan '.$this->planApplication->plan_no.' has been '.$label.' by the council.')
            ->when(
                $this->planApplication->status === PlanApplication::STATUS_REVISION_REQUESTED,
                fn ($mail) => $mail->line('Please review the reviewer comments and resubmit your corrected plans.')
            );
    }

    public function toSms(mixed $notifiable): string
    {
        $label = str_replace('_', ' ', $this->planApplication->status);

        return "Architecture263: Plan {$this->planApplication->plan_no} was {$label}.";
    }
}
