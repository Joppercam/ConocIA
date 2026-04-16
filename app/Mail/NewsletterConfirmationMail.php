<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $confirmationUrl,
        public string $unsubscribeUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirma tu suscripción a ConocIA',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletter.confirmation',
            with: [
                'confirmationUrl' => $this->confirmationUrl,
                'unsubscribeUrl'  => $this->unsubscribeUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
