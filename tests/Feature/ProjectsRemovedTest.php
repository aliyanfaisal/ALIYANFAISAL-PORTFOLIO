<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProjectsRemovedTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_projects_urls_redirect_to_the_home_page(): void
    {
        $this->get('/projects')->assertRedirect('/')->assertStatus(301);
        $this->get('/projects/download')->assertRedirect('/')->assertStatus(301);
    }

    public function test_no_page_links_to_projects_and_the_sitemap_omits_it(): void
    {
        Http::fake(['*' => Http::response('', 500)]);

        foreach (['/', '/about', '/contact', '/products', '/open-source'] as $url) {
            $this->get($url)->assertOk()->assertDontSee(url('/projects'), false);
        }

        $this->assertStringNotContainsString('/projects', $this->get('/sitemap.xml')->getContent());
    }
}
