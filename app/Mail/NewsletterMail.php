<?php

namespace App\Mail;

use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $news;
    public $subject;
    public $token;
    public $featuredNews;
    public $researches;
    public $columns;
    public $subscriber;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        Collection $news, 
        string $subject, 
        string $token, 
        ?Collection $featuredNews = null,
        ?Collection $researches = null,
        ?Collection $columns = null,
        ?Newsletter $subscriber = null
    ) {
        $this->news = $news;
        $this->subject = $subject;
        $this->token = $token;
        $this->featuredNews = $featuredNews ?: collect();
        $this->researches = $researches ?: collect();
        $this->columns = $columns ?: collect();
        $this->subscriber = $subscriber;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.newsletter')
                    ->with([
                        'unsubscribeUrl' => route('newsletter.unsubscribe', ['token' => $this->token]),
                        'viewInBrowserUrl' => route('newsletter.view', ['token' => $this->token])
                    ]);
    }
}