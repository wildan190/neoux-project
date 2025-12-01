<?php

namespace App\Modules\Auth\Application\Mail;

use App\Modules\User\Domain\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function build()
    {
        return $this->subject('Welcome to Prodexa — Your Production Management Platform')
            ->view('emails.welcome');
    }
}
