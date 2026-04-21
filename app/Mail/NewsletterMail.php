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
    public $subject;
    public $featuredNews;
    public $papers;
    public $startup;
    public $researches;
    public $subscriber;

    public function __construct(
        Collection $news,
        ?string $subject = null,
        string $token,
        ?Collection $featuredNews = null,
        ?Collection $papers = null,
        $startup = null,
        ?Collection $researches = null,
        $subscriber = null
    ) {
        $this->news             = $news;
        $this->subject          = $subject ?? 'Novedades de ConocIA';
        $this->unsubscribeToken = $token;
        $this->featuredNews     = $featuredNews ?? collect();
        $this->papers           = $papers ?? collect();
        $this->startup          = $startup;
        $this->researches       = $researches ?? collect();
        $this->subscriber       = $subscriber;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.newsletter')
            ->with([
                'news'             => $this->news,
                'subject'          => $this->subject,
                'unsubscribeToken' => $this->unsubscribeToken,
                'featuredNews'     => $this->featuredNews,
                'papers'           => $this->papers,
                'startup'          => $this->startup,
                'researches'       => $this->researches,
                'subscriber'       => $this->subscriber,
            ]);
    }
}
