<?php

namespace Tests\Feature\Api;

use App\Jobs\PushBlogPostToCuelara;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogPostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.blog_api.token' => 'test-token']);
    }

    private function headers(): array
    {
        return ['Authorization' => 'Bearer test-token'];
    }

    public function test_it_rejects_requests_without_a_valid_token(): void
    {
        $this->postJson('/api/blog-posts', ['title' => 'Post', 'body' => 'Body'])
            ->assertStatus(401);

        $this->postJson('/api/blog-posts', ['title' => 'Post', 'body' => 'Body'], [
            'Authorization' => 'Bearer wrong-token',
        ])->assertStatus(401);
    }

    public function test_it_validates_required_fields(): void
    {
        $this->postJson('/api/blog-posts', [], $this->headers())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'body']);
    }

    public function test_it_publishes_immediately_when_auto_approve_is_enabled(): void
    {
        Setting::current()->update(['auto_approve_posts' => true]);

        $response = $this->postJson('/api/blog-posts', [
            'title' => 'My First Post',
            'body' => "Paragraph one.\n\nParagraph two.",
            'categories' => ['Laravel', 'Laravel'],
            'tags' => ['php'],
        ], $this->headers());

        $response->assertStatus(201)->assertJson([
            'status' => 'published',
            'slug' => 'my-first-post',
            'categories' => ['Laravel'],
            'tags' => ['php'],
        ]);

        $post = BlogPost::where('slug', 'my-first-post')->firstOrFail();
        $this->assertNotNull($post->published_at);
        $this->assertTrue($post->published_at->isPast());
    }

    public function test_it_saves_as_a_draft_when_auto_approve_is_disabled(): void
    {
        Setting::current()->update(['auto_approve_posts' => false]);

        $response = $this->postJson('/api/blog-posts', [
            'title' => 'Needs Review',
            'body' => 'Body text.',
            'published_at' => now()->toIso8601String(),
        ], $this->headers());

        $response->assertStatus(201)->assertJson(['status' => 'pending_review']);

        $post = BlogPost::where('slug', 'needs-review')->firstOrFail();
        $this->assertNull($post->published_at);
        $this->assertSame('draft', $post->status);
    }

    public function test_it_saves_as_a_draft_when_status_is_explicitly_requested(): void
    {
        Setting::current()->update(['auto_approve_posts' => true]);

        $response = $this->postJson('/api/blog-posts', [
            'title' => 'Save For Later',
            'body' => 'Body text.',
            'status' => 'draft',
        ], $this->headers());

        $response->assertStatus(201)->assertJson(['status' => 'draft']);

        $post = BlogPost::where('slug', 'save-for-later')->firstOrFail();
        $this->assertNull($post->published_at);
        $this->assertSame('draft', $post->status);
    }

    public function test_it_does_not_sync_a_draft_to_cuelara(): void
    {
        Queue::fake();
        Setting::current()->update(['auto_approve_posts' => true]);

        $this->postJson('/api/blog-posts', [
            'title' => 'Draft Not Synced',
            'body' => 'Body text.',
            'status' => 'draft',
        ], $this->headers())->assertStatus(201);

        Queue::assertNotPushed(PushBlogPostToCuelara::class);
    }

    public function test_it_derives_a_plain_text_excerpt_from_markdown_body(): void
    {
        $response = $this->postJson('/api/blog-posts', [
            'title' => 'Markdown Excerpt',
            'body' => "## Heading\n\nSome **bold** text with a ```code``` span.",
        ], $this->headers());

        $response->assertStatus(201);

        $post = BlogPost::where('slug', 'markdown-excerpt')->firstOrFail();
        $this->assertStringNotContainsString('##', $post->excerpt);
        $this->assertStringNotContainsString('**', $post->excerpt);
        $this->assertStringContainsString('Heading', $post->excerpt);
    }

    public function test_it_generates_a_unique_slug_on_collision(): void
    {
        BlogPost::create(['title' => 'Dup', 'slug' => 'dup', 'body' => 'x', 'published_at' => now()]);

        $response = $this->postJson('/api/blog-posts', [
            'title' => 'Dup',
            'body' => 'Body text.',
        ], $this->headers());

        $response->assertStatus(201)->assertJson(['slug' => 'dup-2']);
    }

    public function test_it_downloads_and_stores_a_remote_image(): void
    {
        Storage::fake('public');

        Http::fake([
            'https://example.com/cover.jpg' => Http::response(
                file_get_contents(base_path('public/images/aliyan-headshot-cutout.png')),
                200,
                ['Content-Type' => 'image/jpeg'],
            ),
        ]);

        $response = $this->postJson('/api/blog-posts', [
            'title' => 'With Image',
            'body' => 'Body text.',
            'image_url' => 'https://example.com/cover.jpg',
        ], $this->headers());

        $response->assertStatus(201);

        $post = BlogPost::where('slug', 'with-image')->firstOrFail();
        $this->assertSame('blog/with-image.webp', $post->image_path);
        $this->assertSame('https://example.com/cover.jpg', $post->source_image_url);
        Storage::disk('public')->assertExists('blog/with-image.webp');

        // 699x764 source: cropped to 16:9 and never upscaled.
        $this->assertSame(699, $post->image_width);
        $this->assertSame(393, $post->image_height);
        $this->assertSame([699, 393], array_slice(getimagesizefromstring(Storage::disk('public')->get($post->image_path)), 0, 2));
    }

    public function test_it_returns_a_clear_error_when_the_image_download_fails(): void
    {
        Http::fake([
            'https://example.com/missing.jpg' => Http::response('Not Found', 404),
        ]);

        $response = $this->postJson('/api/blog-posts', [
            'title' => 'Broken Image',
            'body' => 'Body text.',
            'image_url' => 'https://example.com/missing.jpg',
        ], $this->headers());

        $response->assertStatus(422)->assertJsonValidationErrors(['image_url']);
        $this->assertDatabaseMissing('blog_posts', ['slug' => 'broken-image']);
    }

    public function test_recent_images_rejects_requests_without_a_valid_token(): void
    {
        $this->getJson('/api/blog-posts/recent-images')->assertStatus(401);
    }

    public function test_recent_images_returns_an_empty_list_when_there_are_no_posts(): void
    {
        $this->getJson('/api/blog-posts/recent-images', $this->headers())
            ->assertOk()
            ->assertExactJson(['recent_images' => []]);
    }

    public function test_recent_images_returns_the_latest_twenty_posts_newest_first(): void
    {
        foreach (range(1, 22) as $number) {
            BlogPost::create([
                'title' => "Post {$number}",
                'slug' => "post-{$number}",
                'body' => 'x',
                'image_path' => "blog/post-{$number}.jpg",
                'source_image_url' => "https://images.unsplash.com/photo-{$number}",
                'created_at' => now()->subDays(30 - $number),
            ]);
        }

        $response = $this->getJson('/api/blog-posts/recent-images', $this->headers())->assertOk();

        $response->assertJsonCount(20, 'recent_images');
        $response->assertJsonPath('recent_images.0', [
            'blog_url' => url('/blog/post-22'),
            'source_image_url' => 'https://images.unsplash.com/photo-22',
            'rehosted_image_url' => asset('storage/blog/post-22.jpg'),
        ]);
        $response->assertJsonPath('recent_images.19.blog_url', url('/blog/post-3'));
    }

    public function test_recent_images_returns_null_for_posts_without_images(): void
    {
        BlogPost::create(['title' => 'Legacy', 'slug' => 'legacy', 'body' => 'x']);

        $this->getJson('/api/blog-posts/recent-images', $this->headers())
            ->assertOk()
            ->assertJsonPath('recent_images.0.source_image_url', null)
            ->assertJsonPath('recent_images.0.rehosted_image_url', null);
    }

    public function test_it_reuses_an_image_already_uploaded_to_this_server(): void
    {
        Storage::fake('public');
        Http::fake();
        Storage::disk('public')->put('generated/hero.png', file_get_contents(base_path('public/images/aliyan-headshot-cutout.png')));

        $response = $this->postJson('/api/blog-posts', [
            'title' => 'Reuse Post',
            'body' => 'Body',
            'image_url' => asset('storage/generated/hero.png'),
        ], $this->headers());

        $response->assertStatus(201)
            ->assertJsonPath('image_url', asset('storage/blog/reuse-post.webp'));

        Http::assertNothingSent();
        $this->assertSame('blog/reuse-post.webp', BlogPost::firstOrFail()->image_path);
        Storage::disk('public')->assertExists('blog/reuse-post.webp');
    }

    public function test_it_does_not_reuse_paths_that_escape_the_upload_directory(): void
    {
        Storage::fake('public');
        Http::fake(['*' => Http::response(file_get_contents(base_path('public/images/aliyan-headshot-cutout.png')), 200, ['Content-Type' => 'image/png'])]);
        Storage::disk('public')->put('secret.png', 'x');

        $this->postJson('/api/blog-posts', [
            'title' => 'Traversal Post',
            'body' => 'Body',
            'image_url' => asset('storage/generated/../secret.png'),
        ], $this->headers())->assertStatus(201);

        $this->assertSame('blog/traversal-post.webp', BlogPost::firstOrFail()->image_path);
    }

    public function test_it_queues_a_push_to_cuelara_by_default(): void
    {
        Queue::fake();

        $this->postJson('/api/blog-posts', ['title' => 'Queued', 'body' => 'Body'], $this->headers())
            ->assertStatus(201);

        Queue::assertPushed(PushBlogPostToCuelara::class);
    }

    public function test_it_skips_the_cuelara_push_when_disabled(): void
    {
        Queue::fake();

        $this->postJson('/api/blog-posts', [
            'title' => 'Not Queued',
            'body' => 'Body',
            'send_to_cuelara' => false,
        ], $this->headers())->assertStatus(201);

        Queue::assertNotPushed(PushBlogPostToCuelara::class);
    }

    public function test_index_rejects_requests_without_a_valid_token(): void
    {
        $this->getJson('/api/blog-posts')->assertStatus(401);
    }

    public function test_index_lists_drafts_and_published_posts_together(): void
    {
        BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'body' => 'x', 'published_at' => null]);
        BlogPost::create(['title' => 'Live', 'slug' => 'live', 'body' => 'x', 'published_at' => now()]);

        $response = $this->getJson('/api/blog-posts', $this->headers())->assertOk();

        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('meta.total', 2);
    }

    public function test_index_filters_by_status(): void
    {
        BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'body' => 'x', 'published_at' => null]);
        BlogPost::create(['title' => 'Live', 'slug' => 'live', 'body' => 'x', 'published_at' => now()]);

        $response = $this->getJson('/api/blog-posts?status=draft', $this->headers())->assertOk();

        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.slug', 'draft');
    }

    public function test_show_returns_the_full_post(): void
    {
        BlogPost::create([
            'title' => 'A Post', 'slug' => 'a-post', 'excerpt' => 'Ex', 'body' => 'Full body', 'published_at' => null,
        ]);

        $response = $this->getJson('/api/blog-posts/a-post', $this->headers())->assertOk();

        $response->assertJson([
            'title' => 'A Post',
            'slug' => 'a-post',
            'status' => 'draft',
            'excerpt' => 'Ex',
            'body' => 'Full body',
        ]);
    }

    public function test_show_returns_404_for_an_unknown_slug(): void
    {
        $this->getJson('/api/blog-posts/missing', $this->headers())->assertNotFound();
    }

    public function test_update_can_change_content_fields(): void
    {
        BlogPost::create(['title' => 'Old Title', 'slug' => 'old-title', 'body' => 'Old body', 'published_at' => null]);

        $response = $this->patchJson('/api/blog-posts/old-title', [
            'title' => 'New Title',
            'body' => 'New body',
        ], $this->headers())->assertOk();

        $response->assertJson(['title' => 'New Title', 'body' => 'New body']);
        $this->assertSame('New Title', BlogPost::where('slug', 'old-title')->firstOrFail()->title);
    }

    public function test_update_can_resync_categories_and_tags(): void
    {
        $post = BlogPost::create(['title' => 'Post', 'slug' => 'post', 'body' => 'x', 'published_at' => null]);

        $response = $this->patchJson('/api/blog-posts/post', [
            'categories' => ['Laravel'],
            'tags' => ['php', 'eloquent'],
        ], $this->headers())->assertOk();

        $response->assertJson(['categories' => ['Laravel'], 'tags' => ['php', 'eloquent']]);
        $this->assertSame(['Laravel'], $post->categories()->pluck('name')->all());
    }

    public function test_update_publishing_a_draft_syncs_it_to_cuelara(): void
    {
        Queue::fake();
        $post = BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'body' => 'x', 'published_at' => null]);

        $response = $this->patchJson('/api/blog-posts/draft', ['status' => 'published'], $this->headers())->assertOk();

        $response->assertJson(['status' => 'published']);
        $this->assertNotNull($post->fresh()->published_at);
        Queue::assertPushed(PushBlogPostToCuelara::class);
    }

    public function test_update_can_unpublish_a_post_back_to_draft(): void
    {
        Queue::fake();
        BlogPost::create(['title' => 'Live', 'slug' => 'live', 'body' => 'x', 'published_at' => now()]);

        $response = $this->patchJson('/api/blog-posts/live', ['status' => 'draft'], $this->headers())->assertOk();

        $response->assertJson(['status' => 'draft']);
        $this->assertNull(BlogPost::where('slug', 'live')->firstOrFail()->published_at);
    }

    public function test_update_returns_404_for_an_unknown_slug(): void
    {
        $this->patchJson('/api/blog-posts/missing', ['title' => 'x'], $this->headers())->assertNotFound();
    }

    public function test_destroy_deletes_the_post(): void
    {
        BlogPost::create(['title' => 'Gone', 'slug' => 'gone', 'body' => 'x', 'published_at' => null]);

        $this->deleteJson('/api/blog-posts/gone', [], $this->headers())
            ->assertOk()
            ->assertJson(['status' => 'deleted']);

        $this->assertDatabaseMissing('blog_posts', ['slug' => 'gone']);
    }

    public function test_destroy_rejects_requests_without_a_valid_token(): void
    {
        $post = BlogPost::create(['title' => 'Gone', 'slug' => 'gone', 'body' => 'x', 'published_at' => null]);

        $this->deleteJson('/api/blog-posts/gone')->assertStatus(401);
        $this->assertNotNull($post->fresh());
    }

    public function test_destroy_redirects_the_old_url_when_the_post_was_published(): void
    {
        BlogPost::create(['title' => 'Live', 'slug' => 'live', 'body' => 'x', 'published_at' => now()]);

        $this->deleteJson('/api/blog-posts/live', [], $this->headers())->assertOk();

        $this->get('/blog/live')->assertRedirect('/blog')->assertStatus(301);
    }

    public function test_categories_accept_names_or_objects_with_a_description(): void
    {
        Category::create(['name' => 'Curated', 'slug' => 'curated', 'description' => 'Hand-written.']);

        $this->postJson('/api/blog-posts', [
            'title' => 'Mixed Categories',
            'body' => 'Body',
            'categories' => [
                'Plain',
                ['name' => 'RAG Systems', 'description' => 'Retrieval-augmented generation in production.'],
                ['name' => 'Curated', 'description' => 'Should not overwrite.'],
            ],
        ], $this->headers())->assertStatus(201);

        $this->assertEqualsCanonicalizing(['Plain', 'RAG Systems', 'Curated'], \App\Models\BlogPost::firstOrFail()->categories->pluck('name')->all());

        $this->assertDatabaseHas('categories', ['slug' => 'plain', 'description' => null]);
        $this->assertDatabaseHas('categories', ['slug' => 'rag-systems', 'description' => 'Retrieval-augmented generation in production.']);
        $this->assertDatabaseHas('categories', ['slug' => 'curated', 'description' => 'Hand-written.']);
    }

    public function test_it_fills_in_a_missing_description_on_an_existing_category(): void
    {
        Category::create(['name' => 'RAG', 'slug' => 'rag']);

        $this->postJson('/api/blog-posts', [
            'title' => 'Fill', 'body' => 'Body', 'categories' => [['name' => 'RAG', 'description' => 'Now described.']],
        ], $this->headers())->assertStatus(201);

        $this->assertDatabaseHas('categories', ['slug' => 'rag', 'description' => 'Now described.']);
    }

    public function test_it_rejects_invalid_category_shapes(): void
    {
        $this->postJson('/api/blog-posts', ['title' => 'T', 'body' => 'B', 'categories' => [['description' => 'no name']]], $this->headers())
            ->assertStatus(422)->assertJsonValidationErrors(['categories.0']);
        $this->postJson('/api/blog-posts', ['title' => 'T', 'body' => 'B', 'categories' => [['name' => 'X', 'description' => str_repeat('a', 301)]]], $this->headers())
            ->assertStatus(422)->assertJsonValidationErrors(['categories.0']);
    }
}
