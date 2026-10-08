<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class OrderPlaced extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pedido recibido — '.$this->order->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order-placed',
            with: $this->order->paymentMethod() === 'transfer'
                ? ['receiptUploadUrl' => URL::temporarySignedRoute('checkout.receipts.create', now()->addDays(7), ['order' => $this->order])]
                : [],
        );
    }
}
