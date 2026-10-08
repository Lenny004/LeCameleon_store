<?php

namespace App\Mail;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferBuyerResponded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Offer $offer, public string $response) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Cliente respondió a una oferta — '.$this->offer->product->name);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.offer-buyer-responded');
    }
}
