<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_valid_xml_including_static_pages_and_products(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);

        $body = $response->getContent();
        foreach (['home', 'about', 'projects.index', 'products.index', 'services.index', 'contact.create'] as $route) {
            $this->assertStringContainsString('<loc>'.route($route).'</loc>', $body);
        }
        $this->assertStringContainsString(route('products.show', 'cuelara'), $body);
    }

    public function test_service_pages_are_listed(): void
    {
        $this->seed(\Database\Seeders\ServiceSeeder::class);

        $service = Service::first();
        $this->assertNotNull($service);
        $this->assertStringContainsString(route('services.show', $service), $this->get('/sitemap.xml')->getContent());
    }
}
