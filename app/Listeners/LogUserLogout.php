<?php

namespace App\Listeners;

use App\Services\UserActivityLogger;
use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        UserActivityLogger::logLogout(
            user: $event->user,
            request: request()
        );
    }
}
