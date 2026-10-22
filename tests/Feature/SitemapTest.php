<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_valid_xml_including_static_pages_and_published_posts(): void
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

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/xml; charset=UTF-8');

        $xml = $response->getContent();
        $this->assertNotFalse(simplexml_load_string($xml), 'sitemap.xml is not valid XML');

        $this->assertStringContainsString(route('home'), $xml);
        $this->assertStringContainsString(route('blog.index'), $xml);
        $this->assertStringContainsString(route('blog.show', $published), $xml);
        $this->assertStringNotContainsString('draft-post', $xml);
    }

    public function test_it_lists_categories_with_published_posts_and_their_last_modified_date(): void
    {
        $post = BlogPost::create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);
        $post->forceFill(['updated_at' => '2026-09-20 10:00:00'])->saveQuietly();

        $category = Category::create(['name' => 'Developer Productivity', 'slug' => 'developer-productivity']);
        $category->forceFill(['updated_at' => '2026-09-01 10:00:00'])->saveQuietly();
        $post->categories()->attach($category);
        Category::create(['name' => 'Empty', 'slug' => 'empty']);

        $xml = $this->get('/sitemap.xml')->getContent();

        $this->assertStringContainsString('/blog/category/developer-productivity', $xml);
        $this->assertStringNotContainsString('/blog/category/empty', $xml);
        $this->assertMatchesRegularExpression(
            '#<loc>[^<]*/blog/category/developer-productivity</loc>\s*<lastmod>2026-09-20T10:00:00\+05:00</lastmod>#',
            $xml,
        );
    }

    public function test_post_last_modified_is_not_bumped_by_a_page_view(): void
    {
        $post = BlogPost::create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);
        $post->forceFill(['updated_at' => '2026-09-20 10:00:00'])->saveQuietly();

        $this->get('/blog/published-post')->assertOk();

        $this->assertStringContainsString('<lastmod>2026-09-20T10:00:00+05:00</lastmod>', $this->get('/sitemap.xml')->getContent());
    }
}
