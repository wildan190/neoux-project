<?php

namespace Modules\Auth\Listeners;

use Modules\Auth\Jobs\SendWelcomeEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeEmailListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        SendWelcomeEmail::dispatch($event->user);
    }
}
