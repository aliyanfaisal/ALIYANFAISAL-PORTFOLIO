<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreelanceSplitTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_services_urls_permanently_redirect_to_the_freelance_site(): void
    {
        $base = rtrim(config('seo.freelance_url'), '/');

        $this->get('/services')->assertRedirect($base.'/services')->assertStatus(301);
        $this->get('/services/llm-integration')->assertRedirect($base.'/services/llm-integration')->assertStatus(301);
    }

    public function test_main_site_has_no_hiring_or_marketplace_calls_to_action(): void
    {
        foreach (['/', '/about', '/contact'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            foreach (['hire me', 'fiverr.com', 'upwork.com', 'wa.me/', 'start a project'] as $needle) {
                $this->assertStringNotContainsString($needle, strtolower($html), "{$url} still contains {$needle}");
            }
            $this->assertStringNotContainsString(config('seo.freelance_url'), $html, "{$url} links to the freelance site");
            $this->assertStringNotContainsString('freelance.aliyanfaisal.com', $html);
        }
    }
}
