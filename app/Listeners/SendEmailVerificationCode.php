<?php

namespace App\Listeners;

use App\Events\Registered;
use App\Mail\VerificationCodeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailVerificationCode implements ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(Registered $event): void
    {
        Mail::to($event->email)->send(new VerificationCodeMail($event->verificationCode));
    }
}
