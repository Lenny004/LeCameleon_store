<?php

namespace App\Mail;

use App\Models\PaymentReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PaymentReceipt $receipt) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Comprobante de pago rechazado — '.$this->receipt->order->number);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.payment-receipt-rejected');
    }
}
