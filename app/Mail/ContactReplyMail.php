<?php

namespace App\Mail;

use App\Models\ContactMessageReply;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessageReply $reply)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->reply->subject,
            replyTo: array_filter([Setting::adminRecipient()]),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-reply', with: ['contactMessage' => $this->reply->contactMessage]);
    }
}
