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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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

    /**
     * A send that fails for good must not count as delivered, so those posts can go out on a later run.
     */
    public function failed(Throwable $exception): void
    {
        DB::table('newsletter_deliveries')
            ->where('newsletter_id', $this->newsletter->id)
            ->where('newsletter_subscriber_id', $this->subscriber->id)
            ->delete();

        Log::error('Newsletter email failed permanently.', [
            'newsletter_id' => $this->newsletter->id,
            'subscriber_id' => $this->subscriber->id,
            'message' => $exception->getMessage(),
        ]);
    }
}
