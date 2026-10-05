<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_only_published_posts(): void
    {
        $published = BlogPost::create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);

        BlogPost::create([
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'body' => 'Body',
            'published_at' => null,
        ]);

        BlogPost::create([
            'title' => 'Future Post',
            'slug' => 'future-post',
            'body' => 'Body',
            'published_at' => now()->addDay(),
        ]);

        $this->get('/blog')
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee('Draft Post')
            ->assertDontSee('Future Post');
    }

    public function test_category_page_filters_posts_by_the_given_category(): void
    {
        $laravel = Category::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $wordpress = Category::create(['name' => 'WordPress', 'slug' => 'wordpress']);

        $laravelPost = BlogPost::create([
            'title' => 'Laravel Tips',
            'slug' => 'laravel-tips',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);
        $laravelPost->categories()->attach($laravel);

        $wordpressPost = BlogPost::create([
            'title' => 'WordPress Tips',
            'slug' => 'wordpress-tips',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);
        $wordpressPost->categories()->attach($wordpress);

        $this->get('/blog/category/laravel')
            ->assertOk()
            ->assertSee('Laravel Tips')
            ->assertDontSee('WordPress Tips');
    }

    public function test_category_page_returns_404_for_an_unknown_category_slug(): void
    {
        $this->get('/blog/category/does-not-exist')->assertNotFound();
    }

    public function test_index_no_longer_filters_by_a_category_query_string(): void
    {
        $laravel = Category::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $laravelPost = BlogPost::create([
            'title' => 'Laravel Tips',
            'slug' => 'laravel-tips',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);
        $laravelPost->categories()->attach($laravel);

        $otherPost = BlogPost::create([
            'title' => 'Unrelated Post',
            'slug' => 'unrelated-post',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog?category=laravel')
            ->assertOk()
            ->assertSee('Laravel Tips')
            ->assertSee('Unrelated Post');
    }

    public function test_index_filters_by_search_query(): void
    {
        BlogPost::create([
            'title' => 'Building AI Chatbots',
            'slug' => 'building-ai-chatbots',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);

        BlogPost::create([
            'title' => 'WooCommerce Setup Guide',
            'slug' => 'woocommerce-setup-guide',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog?q=chatbot')
            ->assertOk()
            ->assertSee('Building AI Chatbots')
            ->assertDontSee('WooCommerce Setup Guide');
    }

    public function test_show_displays_a_published_post(): void
    {
        $post = BlogPost::create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'excerpt' => 'An excerpt.',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog/'.$post->slug)
            ->assertOk()
            ->assertSee($post->title);
    }

    public function test_show_increments_the_view_count(): void
    {
        $post = BlogPost::create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog/'.$post->slug);
        $this->get('/blog/'.$post->slug);

        $this->assertSame(2, $post->fresh()->views);
    }

    public function test_show_returns_404_for_an_unpublished_post(): void
    {
        $post = BlogPost::create([
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'body' => 'Body',
            'published_at' => null,
        ]);

        $this->get('/blog/'.$post->slug)->assertNotFound();
    }

    public function test_show_returns_404_for_a_slug_that_never_existed(): void
    {
        $this->get('/blog/never-existed')->assertNotFound();
    }

    public function test_deleting_a_published_post_redirects_its_old_url_to_its_category(): void
    {
        $category = Category::create(['name' => 'Laravel', 'slug' => 'laravel']);
        $post = BlogPost::create([
            'title' => 'Old Post', 'slug' => 'old-post', 'body' => 'Body', 'published_at' => now()->subDay(),
        ]);
        $post->categories()->attach($category);

        $post->delete();

        $this->get('/blog/old-post')
            ->assertRedirect('/blog/category/laravel')
            ->assertStatus(301);
    }

    public function test_deleting_an_uncategorized_published_post_redirects_to_the_blog_index(): void
    {
        $post = BlogPost::create([
            'title' => 'Old Post', 'slug' => 'old-post', 'body' => 'Body', 'published_at' => now()->subDay(),
        ]);

        $post->delete();

        $this->get('/blog/old-post')->assertRedirect('/blog')->assertStatus(301);
    }

    public function test_deleting_a_draft_that_was_never_published_does_not_create_a_redirect(): void
    {
        $post = BlogPost::create([
            'title' => 'Draft', 'slug' => 'never-live', 'body' => 'Body', 'published_at' => null,
        ]);

        $post->delete();

        $this->get('/blog/never-live')->assertNotFound();
    }

    public function test_a_new_post_reusing_a_deleted_slug_reclaims_it_instead_of_redirecting(): void
    {
        $old = BlogPost::create([
            'title' => 'Old Post', 'slug' => 'reused-slug', 'body' => 'Body', 'published_at' => now()->subDay(),
        ]);
        $old->delete();

        BlogPost::create([
            'title' => 'New Post', 'slug' => 'reused-slug', 'body' => 'Body', 'published_at' => now()->subDay(),
        ]);

        $this->get('/blog/reused-slug')->assertOk()->assertSee('New Post');
    }

    public function test_show_promotes_faq_questions_to_h3_and_emits_schema(): void
    {
        $post = BlogPost::create([
            'title' => 'Fix Guide',
            'slug' => 'fix-guide',
            'body' => "Intro.\n\n## Frequently Asked Questions\n\n**Why does it loop?**\n\nBecause state changes.\n\n**Is it only useState?**\n\nNo, any state update.\n\n## Conclusion\n\n**Bold note?**\n\nStays a paragraph.",
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/blog/'.$post->slug)->assertOk();

        $response->assertSee('<h3 id="why-does-it-loop">Why does it loop?</h3>', false);
        $response->assertSee('<h3 id="is-it-only-usestate">Is it only useState?</h3>', false);
        $response->assertSee('<p><strong>Bold note?</strong></p>', false);
        // FAQ rich results are no longer shown for sites like this one, so no FAQPage schema is emitted.
        $response->assertDontSee('FAQPage', false);
        $response->assertSee('"@type":"BreadcrumbList"', false);
        $response->assertSee('"@context":"https://schema.org"', false);
        $response->assertSee('aria-label="Breadcrumb"', false);
    }

    public function test_show_omits_faq_schema_when_the_post_has_no_faq_section(): void
    {
        $post = BlogPost::create([
            'title' => 'Plain',
            'slug' => 'plain',
            'body' => 'Just text.',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog/'.$post->slug)->assertOk()->assertDontSee('FAQPage', false);
    }
}
