<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockAvailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Producto disponible — '.$this->product->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.stock-available',
        );
    }
}
