<?php

namespace App\Console\Commands;

use App\Enums\NewsletterFrequency;
use App\Services\NewsletterSender;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('newsletter:send')]
#[Description('Send today\'s due newsletters (daily, twice-weekly and weekly) to their subscribers')]
class SendNewsletters extends Command
{
    public function handle(NewsletterSender $sender): int
    {
        foreach (NewsletterFrequency::cases() as $frequency) {
            $newsletter = $sender->run($frequency);

            $this->line($newsletter
                ? "{$frequency->label()}: sent to {$newsletter->recipients_count} subscriber(s)."
                : "{$frequency->label()}: nothing to send.");
        }

        return self::SUCCESS;
    }
}
