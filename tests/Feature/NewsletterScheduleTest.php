<?php

namespace Tests\Feature;

use App\Enums\NewsletterFrequency;
use App\Mail\NewsletterMail;
use App\Models\BlogPost;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(int $views, string $when = '-1 day'): BlogPost
    {
        $post = BlogPost::create([
            'title' => "Post {$views}", 'slug' => "post-{$views}-".uniqid(), 'excerpt' => 'x', 'body' => 'x',
            'status' => 'published', 'published_at' => now()->modify($when),
        ]);
        $post->forceFill(['views' => $views])->saveQuietly();

        return $post;
    }

    public function test_weekly_sends_top_five_by_views_on_monday_only(): void
    {
        Mail::fake();
        collect(range(1, 7))->each(fn ($n) => $this->makePost($n * 10, '-2 days'));
        NewsletterSubscriber::create(['verified_at' => now(), 'email' => 'a@example.com']);

        $this->assertNull(app(NewsletterSender::class)->run(NewsletterFrequency::Weekly, now()->next('Tuesday')));

        $newsletter = app(NewsletterSender::class)->run(NewsletterFrequency::Weekly, now()->next('Monday'));

        $this->assertSame(1, $newsletter->recipients_count);
        Mail::assertQueued(NewsletterMail::class, fn ($m) => $m->posts->count() === 5 && $m->posts->first()->views === 70);
    }

    public function test_a_post_is_never_sent_to_the_same_subscriber_twice(): void
    {
        Mail::fake();
        $this->makePost(10, '-2 hours');
        NewsletterSubscriber::create(['verified_at' => now(), 'email' => 'a@example.com', 'frequency' => 'daily']);

        app(NewsletterSender::class)->run(NewsletterFrequency::Daily, now());
        $second = app(NewsletterSender::class)->run(NewsletterFrequency::Daily, now()->addDay());

        Mail::assertQueued(NewsletterMail::class, 1);
        $this->assertSame(0, $second?->recipients_count ?? 0);
    }

    public function test_unsubscribed_and_other_frequency_subscribers_are_skipped(): void
    {
        Mail::fake();
        $this->makePost(10, '-2 hours');
        NewsletterSubscriber::create(['verified_at' => now(), 'email' => 'a@example.com', 'frequency' => 'daily', 'unsubscribed_at' => now()]);
        NewsletterSubscriber::create(['verified_at' => now(), 'email' => 'b@example.com', 'frequency' => 'weekly']);

        app(NewsletterSender::class)->run(NewsletterFrequency::Daily, now());

        Mail::assertNothingQueued();
    }

    public function test_category_preferences_filter_posts_and_unverified_are_skipped(): void
    {
        Mail::fake();
        $laravel = \App\Models\Category::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $vue = \App\Models\Category::create(['name' => 'Vue', 'slug' => 'vue']);
        $this->makePost(10, '-2 hours')->categories()->attach($laravel);
        $this->makePost(20, '-2 hours')->categories()->attach($vue);

        $picky = NewsletterSubscriber::create(['verified_at' => now(), 'email' => 'picky@example.com', 'frequency' => 'daily']);
        $picky->categories()->attach($laravel);
        NewsletterSubscriber::create(['verified_at' => now(), 'email' => 'all@example.com', 'frequency' => 'daily']);
        NewsletterSubscriber::create(['email' => 'unverified@example.com', 'frequency' => 'daily']);

        app(NewsletterSender::class)->run(NewsletterFrequency::Daily, now());

        Mail::assertQueued(NewsletterMail::class, 2);
        Mail::assertQueued(NewsletterMail::class, fn ($m) => $m->hasTo('picky@example.com') && $m->posts->count() === 1 && $m->posts->first()->views === 10);
        Mail::assertQueued(NewsletterMail::class, fn ($m) => $m->hasTo('all@example.com') && $m->posts->count() === 2);
    }

    public function test_a_cadence_only_runs_once_per_day(): void
    {
        Mail::fake();
        $this->makePost(10, '-2 hours');
        NewsletterSubscriber::create(['verified_at' => now(), 'email' => 'a@example.com', 'frequency' => 'daily']);

        app(NewsletterSender::class)->run(NewsletterFrequency::Daily, now());
        $this->assertNull(app(NewsletterSender::class)->run(NewsletterFrequency::Daily, now()));
        $this->assertSame(1, Newsletter::count());
    }

    public function test_preferences_can_be_changed_and_unsubscribed_via_token(): void
    {
        $subscriber = NewsletterSubscriber::create(['verified_at' => now(), 'email' => 'a@example.com']);
        $this->assertSame(NewsletterFrequency::Weekly, $subscriber->fresh()->frequency);

        $this->get(route('newsletter.preferences', $subscriber->token))->assertOk();
        $this->put(route('newsletter.preferences.update', $subscriber->token), ['frequency' => 'daily', 'topics' => 'all'])->assertRedirect();
        $this->assertSame(NewsletterFrequency::Daily, $subscriber->fresh()->frequency);

        $this->post(route('newsletter.unsubscribe', $subscriber->token))->assertRedirect();
        $this->assertFalse($subscriber->fresh()->isSubscribed());

        $this->get(route('newsletter.preferences', 'bad-token'))->assertNotFound();
    }
}
