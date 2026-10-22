<?php

namespace App\Services;

use App\Enums\NewsletterFrequency;
use App\Mail\NewsletterMail;
use App\Models\BlogPost;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NewsletterSender
{
    /**
     * Runs one cadence for the day: picks its top posts, then mails each active subscriber
     * only the ones they have never been sent. Returns null when the cadence is not due,
     * already ran today, or there is nothing new to say.
     */
    public function run(NewsletterFrequency $frequency, ?CarbonInterface $now = null): ?Newsletter
    {
        $now ??= now();

        if (! $frequency->isDueOn($now)) {
            return null;
        }

        $alreadyRan = Newsletter::where('frequency', $frequency->value)->whereDate('run_date', $now->toDateString())->exists();

        if ($alreadyRan) {
            return null;
        }

        $windowStart = $now->copy()->subDays($frequency->windowDays());
        $candidates = $this->candidatePosts($windowStart, $now);

        if ($candidates->isEmpty()) {
            return null;
        }

        $newsletter = Newsletter::create([
            'frequency' => $frequency,
            'run_date' => $now->toDateString(),
            'subject' => $this->subject($frequency, $candidates->first(), $now),
            'window_start' => $windowStart,
            'window_end' => $now,
        ]);

        $sentPostIds = [];
        $recipients = 0;
        $batchSize = config('newsletter.batch_size');
        $interval = config('newsletter.batch_interval_minutes');

        NewsletterSubscriber::active()
            ->where('frequency', $frequency->value)
            ->chunkById(200, function ($subscribers) use ($newsletter, $candidates, $frequency, $now, $batchSize, $interval, &$sentPostIds, &$recipients): void {
                $delivered = DB::table('newsletter_deliveries')
                    ->whereIn('newsletter_subscriber_id', $subscribers->modelKeys())
                    ->whereIn('blog_post_id', $candidates->modelKeys())
                    ->get()
                    ->groupBy('newsletter_subscriber_id');

                $topics = DB::table('category_newsletter_subscriber')
                    ->whereIn('newsletter_subscriber_id', $subscribers->modelKeys())
                    ->get()
                    ->groupBy('newsletter_subscriber_id');

                foreach ($subscribers as $subscriber) {
                    $seen = $delivered->get($subscriber->id, collect())->pluck('blog_post_id');
                    $categoryIds = $topics->get($subscriber->id, collect())->pluck('category_id');

                    $posts = $candidates
                        ->whereNotIn('id', $seen->all())
                        ->when($categoryIds->isNotEmpty(), fn ($posts) => $posts->filter(
                            fn (BlogPost $post) => $post->categories->pluck('id')->intersect($categoryIds)->isNotEmpty()
                        ))
                        ->take($frequency->postLimit())
                        ->values();

                    if ($posts->isEmpty()) {
                        continue;
                    }

                    DB::table('newsletter_deliveries')->insert($posts->map(fn (BlogPost $post) => [
                        'newsletter_id' => $newsletter->id,
                        'newsletter_subscriber_id' => $subscriber->id,
                        'blog_post_id' => $post->id,
                        'created_at' => $now,
                    ])->all());

                    // Each batch of subscribers is released a few minutes after the previous one to stay under SMTP limits.
                    Mail::to($subscriber->email)->later(now()->addMinutes(intdiv($recipients, $batchSize) * $interval), new NewsletterMail($newsletter, $subscriber, new Collection($posts->all())));

                    $subscriber->forceFill(['last_sent_at' => $now])->saveQuietly();

                    $sentPostIds = array_merge($sentPostIds, $posts->modelKeys());
                    $recipients++;
                }
            });

        $newsletter->posts()->sync(
            collect(array_unique($sentPostIds))
                ->mapWithKeys(fn (int $id) => [$id => ['position' => $candidates->search(fn (BlogPost $p) => $p->id === $id)]])
                ->all()
        );

        $newsletter->update([
            'recipients_count' => $recipients,
            'status' => $recipients > 0 ? 'sent' : 'skipped',
            'sent_at' => $now,
        ]);

        return $newsletter;
    }

    /**
     * Published posts in the window, best first: most viewed, then newest.
     *
     * @return Collection<int, BlogPost>
     */
    private function candidatePosts(CarbonInterface $from, CarbonInterface $to): Collection
    {
        return BlogPost::published()
            ->whereBetween('published_at', [$from, $to])
            ->orderByDesc('views')
            ->orderByDesc('published_at')
            ->limit(100)
            ->with('categories:id')
            ->get();
    }

    private function subject(NewsletterFrequency $frequency, BlogPost $top, CarbonInterface $now): string
    {
        return match ($frequency) {
            NewsletterFrequency::Daily => $top->title,
            NewsletterFrequency::TwiceWeekly => 'New on the blog: '.$top->title,
            NewsletterFrequency::Weekly => 'This week on the blog — '.$now->format('M j'),
        };
    }
}
