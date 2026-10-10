<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

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
        $this->get('/services')->assertDontSee('google-site-verification', false)->assertDontSee('googletagmanager', false);

        config(['seo.google_site_verification' => 'abc123', 'seo.ga4_id' => 'G-TEST123']);

        $this->get('/services')
            ->assertSee('<meta name="google-site-verification" content="abc123">', false)
            ->assertSee('googletagmanager.com/gtag/js?id=G-TEST123', false);
    }

    public function test_sitemap_has_no_changefreq_and_lists_the_main_pages(): void
    {
        $xml = $this->get('/sitemap.xml')->getContent();

        foreach (['home', 'about', 'projects.index', 'services.index', 'contact.create'] as $route) {
            $this->assertStringContainsString('<loc>'.route($route).'</loc>', $xml);
        }
        $this->assertStringNotContainsString('changefreq', $xml);
        $this->assertStringNotContainsString('<priority>', $xml);
        $this->assertStringNotContainsString('/blog', $xml);
    }

    public function test_the_404_page_is_noindex_and_links_home(): void
    {
        $this->get('/nope')->assertNotFound()
            ->assertSee('noindex', false)
            ->assertSee(route('home'), false);
    }

    public function test_there_is_no_blog_or_rss_feed(): void
    {
        $this->get('/blog')->assertNotFound();
        $this->get('/blog/feed')->assertNotFound();
        $this->assertStringNotContainsString('application/rss+xml', $this->get('/services')->getContent());
    }
}
