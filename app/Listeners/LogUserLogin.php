<?php

namespace App\Listeners;

use App\Services\UserActivityLogger;
use Illuminate\Auth\Events\Login;

class LogUserLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        UserActivityLogger::logLogin(
            user: $event->user,
            request: request()
        );
    }
}
