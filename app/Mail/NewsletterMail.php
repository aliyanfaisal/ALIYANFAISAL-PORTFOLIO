<?php

namespace App\Mail;

use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, \App\Models\BlogPost>  $posts
     */
    public function __construct(
        public Newsletter $newsletter,
        public NewsletterSubscriber $subscriber,
        public Collection $posts,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->newsletter->subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.newsletter');
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'List-Unsubscribe' => '<'.route('newsletter.unsubscribe.one-click', $this->subscriber->token).'>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ]);
    }
}
