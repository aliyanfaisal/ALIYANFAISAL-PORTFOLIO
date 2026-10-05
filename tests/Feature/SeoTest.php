<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\BlogPostRedirect;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(array $attributes = []): BlogPost
    {
        return BlogPost::create($attributes + [
            'title' => 'Caching OpenAI API Responses',
            'slug' => 'caching-openai-api-responses',
            'excerpt' => 'How to cache LLM responses.',
            'body' => "Intro.\n\n## One\n\nText\n\n## Two\n\nText\n\n## Three\n\nText\n\n## Four\n\n[External](https://example.org/x)",
            'published_at' => now()->subDays(3),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function jsonLdNodes(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);
        $this->assertNotEmpty($matches[1], 'No JSON-LD found');

        return collect($matches[1])
            ->flatMap(function (string $json): array {
                $decoded = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

                return $decoded['@graph'] ?? [$decoded];
            })
            ->all();
    }

    public function test_post_page_has_canonical_og_image_and_valid_blogposting_json_ld(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('blog/hero.webp', 'x');
        $category = Category::create(['name' => 'LLM Integration', 'slug' => 'llm-integration']);
        $post = $this->makePost(['image_path' => 'blog/hero.webp', 'image_width' => 1600, 'image_height' => 900]);
        $post->categories()->attach($category);
        $post->tags()->attach(Tag::create(['name' => 'OpenAI', 'slug' => 'openai']));

        $html = $this->get('/blog/caching-openai-api-responses')->assertOk()->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="'.url('/blog/caching-openai-api-responses').'">', $html);
        $this->assertStringContainsString('<meta property="og:image" content="'.asset('storage/blog/hero.webp').'">', $html);
        $this->assertStringContainsString('<meta property="og:image:width" content="1600">', $html);
        $this->assertStringContainsString('rel="alternate" type="application/rss+xml"', $html);

        $nodes = collect($this->jsonLdNodes($html));
        $posting = $nodes->firstWhere('@type', 'BlogPosting');

        $this->assertNotNull($posting);
        $this->assertSame(['@id' => url('/').'/#person'], $posting['author']);
        $this->assertSame(['@id' => url('/').'/#person'], $posting['publisher']);
        $this->assertSame('ImageObject', $posting['image']['@type']);
        $this->assertSame(1600, $posting['image']['width']);
        $this->assertSame('LLM Integration', $posting['articleSection']);
        $this->assertSame(['OpenAI'], $posting['keywords']);
        $this->assertSame(['@id' => route('blog.index').'#blog'], $posting['isPartOf']);
        $this->assertNull($nodes->firstWhere('@type', 'FAQPage'));

        $crumbs = $nodes->firstWhere('@type', 'BreadcrumbList')['itemListElement'];
        $this->assertSame(['Home', 'Blog', 'LLM Integration', 'Caching OpenAI API Responses'], array_column($crumbs, 'name'));
        $this->assertNotContains(null, array_column($crumbs, 'item'));
    }

    public function test_post_body_gets_heading_ids_toc_safe_external_links_and_time_elements(): void
    {
        $this->makePost();

        $html = $this->get('/blog/caching-openai-api-responses')->assertOk()->getContent();

        $this->assertStringContainsString('<h2 id="one">One</h2>', $html);
        $this->assertStringContainsString('href="#four"', $html);
        $this->assertStringContainsString('rel="noopener"', $html);
        $this->assertStringNotContainsString('nofollow', $html);
        $this->assertStringContainsString('<time datetime=', $html);
        $this->assertStringContainsString('Written by', $html);
    }

    public function test_related_posts_share_a_category_and_exclude_drafts(): void
    {
        $category = Category::create(['name' => 'AI', 'slug' => 'ai']);
        $this->makePost()->categories()->attach($category);
        $this->makePost(['title' => 'Related One', 'slug' => 'related-one'])->categories()->attach($category);
        $this->makePost(['title' => 'Draft One', 'slug' => 'draft-one', 'status' => 'draft', 'published_at' => null])->categories()->attach($category);

        $html = $this->get('/blog/caching-openai-api-responses')->getContent();

        $this->assertStringContainsString('Related posts', $html);
        $this->assertStringContainsString('Related One', $html);
        $this->assertStringNotContainsString('Draft One', $html);
    }

    public function test_paginated_blog_pages_self_canonicalize_and_strip_tracking_params(): void
    {
        foreach (range(1, 16) as $number) {
            $this->makePost(['title' => "Post {$number}", 'slug' => "post-{$number}", 'published_at' => now()->subMinutes($number)]);
        }

        $page2 = $this->get('/blog?page=2&utm_source=x&gclid=1')->assertOk()->getContent();
        $this->assertStringContainsString('<link rel="canonical" href="'.url('/blog').'?page=2">', $page2);

        $page1 = $this->get('/blog?utm_source=x')->getContent();
        $this->assertStringContainsString('<link rel="canonical" href="'.url('/blog').'">', $page1);
        $this->assertStringContainsString('<h2 class="mt-4', $page1);
        $this->assertStringContainsString('"@type":"Blog"', $page1);
        $this->assertStringContainsString('"@type":"ItemList"', $page1);
    }

    public function test_search_results_are_noindex_and_category_description_is_used(): void
    {
        Category::create(['name' => 'RAG', 'slug' => 'rag', 'description' => 'How retrieval-augmented generation works in production.']);
        $this->makePost()->categories()->attach(Category::first());

        $this->get('/blog?q=cache')->assertSee('<meta name="robots" content="noindex, follow">', false);
        $this->get('/blog')->assertSee('index, follow, max-image-preview:large, max-snippet:-1', false);

        $html = $this->get('/blog/category/rag')->getContent();
        $this->assertStringContainsString('<meta name="description" content="How retrieval-augmented generation works in production.">', $html);
        $this->assertMatchesRegularExpression('#<p[^>]*>\s*How retrieval-augmented generation works in production\.\s*</p>#', $html);
        $this->assertStringContainsString('/blog/category/rag', collect($this->jsonLdNodes($html))->firstWhere('@type', 'BreadcrumbList')['itemListElement'][2]['item']);
    }

    public function test_static_pages_have_canonical_open_graph_unique_descriptions_and_person_graph(): void
    {
        Http::fake(['*' => Http::response('', 500)]);
        $descriptions = [];

        foreach (['/' => 'ProfilePage', '/about' => 'ProfilePage', '/projects' => 'CollectionPage', '/services' => 'CollectionPage'] as $path => $type) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertStringContainsString('<link rel="canonical" href="'.url($path).'">', $html, $path);
            $this->assertStringContainsString('<meta property="og:image" content="'.asset('images/og-default.png').'">', $html);
            $this->assertStringContainsString('<meta name="twitter:card" content="summary_large_image">', $html);
            $this->assertNotNull(collect($this->jsonLdNodes($html))->firstWhere('@type', $type), "{$path} missing {$type}");

            preg_match('#<meta name="description" content="([^"]*)">#', $html, $match);
            $descriptions[$path] = html_entity_decode($match[1]);
        }

        $this->assertCount(4, array_unique($descriptions));
        foreach (['/about', '/projects', '/services'] as $path) {
            $this->assertGreaterThanOrEqual(140, mb_strlen($descriptions[$path]), $path);
            $this->assertLessThanOrEqual(160, mb_strlen($descriptions[$path]), $path);
        }

        $home = collect($this->jsonLdNodes($this->get('/')->getContent()));
        $person = $home->firstWhere('@type', 'Person');
        $this->assertSame(url('/').'/#person', $person['@id']);
        $this->assertStringContainsString('Aliyan Faisal', $this->get('/')->getContent());
    }

    public function test_verification_and_analytics_tags_render_only_when_configured(): void
    {
        $this->get('/blog')->assertDontSee('google-site-verification', false)->assertDontSee('googletagmanager', false);

        config(['seo.google_site_verification' => 'abc123', 'seo.ga4_id' => 'G-TEST123']);

        $this->get('/blog')
            ->assertSee('<meta name="google-site-verification" content="abc123">', false)
            ->assertSee('googletagmanager.com/gtag/js?id=G-TEST123', false);
    }

    public function test_drafts_are_unreachable_and_absent_from_listing_feed_and_sitemap(): void
    {
        $this->makePost();
        $this->makePost(['title' => 'Secret Draft', 'slug' => 'secret-draft', 'status' => 'draft', 'published_at' => null]);

        $this->get('/blog/secret-draft')->assertNotFound();
        $this->get('/blog')->assertDontSee('Secret Draft');
        $this->get('/blog/feed')->assertOk()->assertDontSee('secret-draft')->assertSee('caching-openai-api-responses');
        $this->get('/sitemap.xml')->assertDontSee('secret-draft', false);
    }

    public function test_rss_feed_is_valid_xml_with_the_latest_posts(): void
    {
        $this->makePost();

        $response = $this->get('/blog/feed')->assertOk();

        $this->assertStringContainsString('application/rss+xml', $response->headers->get('Content-Type'));
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $this->assertSame('Caching OpenAI API Responses', (string) $xml->channel->item[0]->title);
    }

    public function test_sitemap_has_images_no_changefreq_and_the_main_pages(): void
    {
        Storage::fake('public');
        $this->makePost(['image_path' => 'blog/hero.webp', 'image_width' => 1600, 'image_height' => 900]);

        $xml = $this->get('/sitemap.xml')->getContent();

        foreach (['home', 'about', 'projects.index', 'services.index', 'blog.index'] as $route) {
            $this->assertStringContainsString('<loc>'.route($route).'</loc>', $xml);
        }
        $this->assertStringContainsString('<image:loc>'.asset('storage/blog/hero.webp').'</image:loc>', $xml);
        $this->assertStringNotContainsString('changefreq', $xml);
        $this->assertStringNotContainsString('<priority>', $xml);
        $this->assertStringNotContainsString('page=', $xml);
    }

    public function test_changing_a_slug_301_redirects_the_old_url(): void
    {
        $post = $this->makePost();

        $post->update(['slug' => 'new-slug']);

        $this->get('/blog/caching-openai-api-responses')->assertRedirect(route('blog.show', $post))->assertStatus(301);
        $this->get('/blog/new-slug')->assertOk();

        $post->update(['slug' => 'newer-slug']);
        $this->assertSame(route('blog.show', ['slug' => 'newer-slug']), BlogPostRedirect::where('from_slug', 'caching-openai-api-responses')->value('to_url'));
    }

    public function test_the_404_page_is_noindex_and_links_home_and_to_the_blog(): void
    {
        $this->get('/nope')->assertNotFound()
            ->assertSee('noindex', false)
            ->assertSee(route('home'), false)
            ->assertSee(route('blog.index'), false);
    }

    public function test_optimize_images_command_converts_to_webp_and_is_safe_to_rerun(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('blog/big.jpg', UploadedFile::fake()->image('big.jpg', 3000, 2000)->getContent());
        $post = $this->makePost(['slug' => 'big']);
        // Saved quietly (bypassing the model's upload hook) to simulate a legacy raw image.
        $post->forceFill(['image_path' => 'blog/big.jpg', 'image_width' => null, 'image_height' => null])->saveQuietly();
        $updatedAt = $post->fresh()->updated_at;

        $this->artisan('blog:optimize-images')->assertSuccessful();

        $post->refresh();
        $this->assertSame('blog/big.webp', $post->image_path);
        $this->assertSame([1600, 900], [$post->image_width, $post->image_height]);
        $this->assertEquals($updatedAt, $post->updated_at);
        Storage::disk('public')->assertExists('blog/big.webp');
        Storage::disk('public')->assertExists('blog/big.jpg');

        $this->artisan('blog:optimize-images')->expectsOutputToContain('Converted: 0, already optimized: 1')->assertSuccessful();
    }
}
