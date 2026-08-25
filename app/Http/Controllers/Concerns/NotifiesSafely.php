<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Log;

/**
 * A failed email/SMS send (e.g. unreachable SMTP server) must never fail
 * the underlying action it's attached to — the state change already
 * happened by the time we notify, so we log and move on instead of
 * bubbling a 500 back for what is just a best-effort side effect.
 */
trait NotifiesSafely
{
    protected function notifySafely(mixed $notifiable, mixed $notification): void
    {
        try {
            $notifiable->notify($notification);
        } catch (\Throwable $e) {
            Log::warning('Notification failed to send', [
                'notifiable' => get_class($notifiable).'#'.$notifiable->getKey(),
                'notification' => get_class($notification),
                'message' => $e->getMessage(),
            ]);
        }
    }
}
