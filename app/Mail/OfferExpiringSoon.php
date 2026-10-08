<?php

namespace App\Mail;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferExpiringSoon extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Offer $offer) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Tu precio acordado está por vencer');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.offer-expiring-soon');
    }
}
