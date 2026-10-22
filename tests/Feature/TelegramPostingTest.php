<?php

namespace Tests\Feature;

use App\Jobs\PostBlogPostToTelegram;
use App\Models\BlogPost;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TelegramPostingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.telegram.bot_token' => 'token']);
        Setting::current()->update(['telegram_chat_id' => '@chan']);
    }

    private function publish(string $slug = 'hello', string $status = 'published'): BlogPost
    {
        return BlogPost::create(['title' => 'Hello <World>', 'slug' => $slug, 'excerpt' => 'Short', 'body' => 'x', 'status' => $status]);
    }

    public function test_publishing_queues_a_telegram_post_immediately_by_default(): void
    {
        Queue::fake();

        $this->publish();

        Queue::assertPushed(PostBlogPostToTelegram::class, fn ($job) => $job->delay === null);
    }

    public function test_drafts_do_not_post_until_they_go_live(): void
    {
        Queue::fake();

        $post = $this->publish('draft-one', 'draft');
        Queue::assertNothingPushed();

        $post->update(['status' => 'published']);
        Queue::assertPushed(PostBlogPostToTelegram::class, 1);
    }

    public function test_the_admin_switch_and_delay_are_respected(): void
    {
        Queue::fake();

        Setting::current()->update(['telegram_enabled' => false]);
        $this->publish('off');
        Queue::assertNothingPushed();

        Setting::current()->update(['telegram_enabled' => true, 'telegram_delay_minutes' => 30]);
        $this->publish('delayed');
        Queue::assertPushed(PostBlogPostToTelegram::class, fn ($job) => $job->delay !== null);
    }

    public function test_the_job_sends_an_escaped_message_once(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);
        $post = $this->publish();
        Http::assertSentCount(1);
        $this->assertNotNull($post->fresh()->telegram_posted_at);

        (new PostBlogPostToTelegram($post))->handle(app(\App\Services\TelegramService::class));

        Http::assertSentCount(1);
        Http::assertSent(fn ($r) => str_contains($r['text'], 'Hello &lt;World&gt;') && $r['chat_id'] === '@chan' && str_contains($r['text'], '/blog/hello'));
    }

    public function test_turning_the_switch_off_cancels_a_waiting_job(): void
    {
        Http::fake();
        $post = BlogPost::withoutEvents(fn () => $this->publish('waiting'));
        Setting::current()->update(['telegram_enabled' => false]);

        (new PostBlogPostToTelegram($post))->handle(app(\App\Services\TelegramService::class));

        Http::assertNothingSent();
    }
}
