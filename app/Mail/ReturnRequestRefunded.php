<?php

namespace App\Mail;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReturnRequestRefunded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ReturnRequest $returnRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Devolución reembolsada');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.return-refunded');
    }
}
