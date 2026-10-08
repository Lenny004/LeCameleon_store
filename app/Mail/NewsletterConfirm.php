<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewsletterConfirm extends NewsletterMailable
{
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirma tu suscripción a Le Cameleon');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.newsletter-confirm');
    }
}
