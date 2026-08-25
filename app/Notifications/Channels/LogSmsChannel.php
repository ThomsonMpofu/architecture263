<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Stub SMS channel: logs the message instead of sending it. Swap this
 * class's send() implementation for a real gateway (e.g. Twilio, Africa's
 * Talking) later without touching any of the Notification classes that use it.
 */
class LogSmsChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $phone = $notifiable->routeNotificationFor('sms') ?? $notifiable->phone ?? null;
        $message = $notification->toSms($notifiable);

        Log::info('[SMS stub] would send SMS', [
            'to' => $phone,
            'message' => $message,
        ]);
    }
}
