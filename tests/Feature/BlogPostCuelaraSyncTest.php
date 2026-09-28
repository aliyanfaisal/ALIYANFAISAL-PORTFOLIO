<?php

namespace Tests\Feature;

use App\Jobs\PushBlogPostToCuelara;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BlogPostCuelaraSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_a_draft_does_not_sync_to_cuelara(): void
    {
        Queue::fake();

        BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'body' => 'Body', 'published_at' => null]);

        Queue::assertNotPushed(PushBlogPostToCuelara::class);
    }

    public function test_editing_a_draft_into_published_syncs_it_to_cuelara(): void
    {
        Queue::fake();

        $post = BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'body' => 'Body', 'published_at' => null]);
        Queue::assertNotPushed(PushBlogPostToCuelara::class);

        $post->update(['published_at' => now()]);

        Queue::assertPushed(PushBlogPostToCuelara::class, fn (PushBlogPostToCuelara $job) => $job->post->is($post));
    }

    public function test_editing_an_already_published_post_does_not_resync_it(): void
    {
        Queue::fake();

        $post = BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'body' => 'Body', 'published_at' => null]);
        $post->update(['published_at' => now()]);
        Queue::assertPushedTimes(PushBlogPostToCuelara::class, 1);

        $post->update(['body' => 'Updated body']);

        Queue::assertPushedTimes(PushBlogPostToCuelara::class, 1);
    }
}
