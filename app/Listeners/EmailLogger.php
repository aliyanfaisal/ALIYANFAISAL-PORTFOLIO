<?php

namespace App\Listeners;

use App\Models\EmailLog;
use App\Models\Setting;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Address;
use Throwable;
use WeakMap;

class EmailLogger
{
    /**
     * @var WeakMap<object, EmailLog>|null
     */
    private static ?WeakMap $pending = null;

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

            self::$pending ??= new WeakMap;
            self::$pending[$event->message] = $log;
        } catch (Throwable $exception) {
            // Logging must never stop an email from being sent.
            report($exception);
        }
    }

    public function sent(MessageSent $event): void
    {
        try {
            $message = $event->sent->getOriginalMessage();
            $log = self::$pending[$message] ?? null;

            if ($log !== null) {
                $log->update(['status' => 'sent']);
                unset(self::$pending[$message]);
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
