<?php

namespace Modules\User\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\TeamInvitationMail;

class SendTeamInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public $invitation,
        public string $email
    ) {
    }

    public function handle(): void
    {
        Mail::to($this->email)
            ->send(new TeamInvitationMail($this->invitation));
    }

    public function retryUntil()
    {
        return now()->addMinutes(10);
    }
}
