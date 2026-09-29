<?php

namespace Tests\Feature;

use App\Jobs\PushBlogPostToCuelara;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScheduledBlogPostPublishingTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(array $attributes = []): BlogPost
    {
        return BlogPost::create(['title' => 'Post', 'slug' => 'post-'.uniqid(), 'body' => 'Body'] + $attributes);
    }

    public function test_publishing_a_draft_uses_the_current_time(): void
    {
        Queue::fake();
        $this->travelTo(now()->startOfMinute());
        $post = $this->makePost(['status' => 'draft']);

        $post->update(['status' => 'published']);

        $this->assertSame('published', $post->fresh()->status);
        $this->assertTrue($post->fresh()->published_at->equalTo(now()));
        Queue::assertPushed(PushBlogPostToCuelara::class);
    }

    public function test_publishing_a_scheduled_draft_early_goes_live_now(): void
    {
        Queue::fake();
        $post = $this->makePost(['status' => 'draft', 'published_at' => now()->addDays(2)]);

        $post->update(['status' => 'published']);

        $this->assertTrue($post->fresh()->published_at->lessThanOrEqualTo(now()));
    }

    public function test_a_draft_with_a_future_date_stays_scheduled(): void
    {
        $post = $this->makePost(['status' => 'draft', 'published_at' => now()->addDay()]);

        $this->assertSame('draft', $post->fresh()->status);
        $this->assertTrue($post->fresh()->published_at->isFuture());
    }

    public function test_reverting_to_draft_clears_a_past_publish_date(): void
    {
        Queue::fake();
        $post = $this->makePost(['status' => 'published']);

        $post->update(['status' => 'draft']);

        $this->assertNull($post->fresh()->published_at);
    }

    public function test_command_publishes_due_scheduled_posts_at_their_scheduled_time(): void
    {
        Queue::fake();
        $due = $this->makePost(['status' => 'draft', 'published_at' => now()->addHour()]);
        $later = $this->makePost(['status' => 'draft', 'published_at' => now()->addDays(3)]);
        $scheduledFor = $due->published_at;

        $this->travelTo(now()->addHours(2));
        $this->artisan('blog:publish-scheduled')->assertSuccessful();

        $this->assertSame('published', $due->fresh()->status);
        $this->assertTrue($due->fresh()->published_at->equalTo($scheduledFor));
        $this->assertSame('draft', $later->fresh()->status);
        Queue::assertPushedTimes(PushBlogPostToCuelara::class, 1);
    }
}
