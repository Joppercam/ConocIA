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
    public $researches;
    public $columns;
    public $subscriber;

    /**
     * Create a new message instance.
     *
     * @param  Collection  $news
     * @param  string|null  $subject
     * @param  string  $token
     * @param  Collection|null  $featuredNews
     * @param  Collection|null  $researches
     * @param  Collection|null  $columns
     * @param  object|null  $subscriber
     * @return void
     */
    public function __construct(
        Collection $news, 
        ?string $subject = null, 
        string $token,
        ?Collection $featuredNews = null,
        ?Collection $researches = null,
        ?Collection $columns = null,
        $subscriber = null
    ) {
        $this->news = $news;
        $this->subject = $subject ?? 'Novedades de ConocIA';
        $this->unsubscribeToken = $token;
        $this->featuredNews = $featuredNews ?? collect();
        $this->researches = $researches ?? collect();
        $this->columns = $columns ?? collect();
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
                'news' => $this->news,
                'subject' => $this->subject,
                'unsubscribeToken' => $this->unsubscribeToken,
                'featuredNews' => $this->featuredNews,
                'researches' => $this->researches,
                'columns' => $this->columns,
                'subscriber' => $this->subscriber
            ]);
    }
}