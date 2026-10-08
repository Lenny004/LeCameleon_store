<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountAlreadyExists extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ya existe una cuenta con este correo — Le Cameleon',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'notifications.account-already-exists',
        );
    }
}
