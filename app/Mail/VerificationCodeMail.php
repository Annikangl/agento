<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected string $verificationCode)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verification Code Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.verifications.code',
            with: [
                'verificationCode' => $this->verificationCode
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
