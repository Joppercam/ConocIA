<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $news;
    public $unsubscribeToken;
    public $emailSubject;

    public function __construct(Collection $news, ?string $subject = null, string $token)
    {
        $this->news = $news;
        $this->subject = $subject ?? 'Novedades de ConocIA'; // Valor predeterminado si es null
        $this->unsubscribeToken = $token;
    }

    public function build()
    {
        return $this->subject($this->subject)
        ->view('emails.newsletter', [
            'news' => $this->news,
            'subject' => $this->emailSubject, 
            'unsubscribeToken' => $this->unsubscribeToken
        ]);
    }
}