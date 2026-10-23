<?php

namespace App\Listeners;

use App\Models\EmailLog;
use App\Models\Setting;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Throwable;

class EmailLogger
{
    /**
     * Symfony clones the message before sending, so the log id travels in a header rather than an object map.
     */
    private const HEADER = 'X-Email-Log-Id';

    public function sending(MessageSending $event): void
    {
        try {
            if (! Setting::current()->email_logging_enabled) {
                return;
            }

            $log = EmailLog::create([
                'to' => collect($event->message->getTo())->map(fn (Address $address) => $address->getAddress())->implode(', '),
                'subject' => $event->message->getSubject(),
                'status' => 'sending',
            ]);

            $event->message->getHeaders()->addTextHeader(self::HEADER, (string) $log->id);
        } catch (Throwable $exception) {
            // Logging must never stop an email from being sent.
            report($exception);
        }
    }

    public function sent(MessageSent $event): void
    {
        try {
            $message = $event->sent->getOriginalMessage();
            $id = $message instanceof Message ? $message->getHeaders()->getHeaderBody(self::HEADER) : null;

            if ($id !== null) {
                EmailLog::whereKey($id)->update(['status' => 'sent']);
            } elseif (Setting::current()->email_logging_enabled) {
                Log::warning('Sent email could not be matched to its email log row.', ['message_class' => get_debug_type($message)]);
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
