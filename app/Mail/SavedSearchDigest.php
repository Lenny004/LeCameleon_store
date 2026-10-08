<?php

namespace App\Mail;

use App\Models\SavedSearch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SavedSearchDigest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public SavedSearch $savedSearch,
        public array $products,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nuevas piezas para tu búsqueda guardada');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.saved-search-digest');
    }
}
