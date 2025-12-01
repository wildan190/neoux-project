<?php

namespace App\Modules\Auth\Application\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Modules\Auth\Application\Jobs\SendWelcomeEmail;

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
