<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_lists_both_products_and_coming_soon(): void
    {
        $this->get('/products')
            ->assertOk()
            ->assertSee('InvoiceInspect')
            ->assertSee('Cuelara')
            ->assertSee('More coming soon');
    }

    public function test_each_product_page_renders_with_its_own_design(): void
    {
        $this->get('/products/invoiceinspect')->assertOk()->assertSee('Find invoice errors')->assertSee('https://invoiceinspect.app');
        $this->get('/products/cuelara')->assertOk()->assertSee('the perfect prompt.')->assertSee('https://cuelara.com');
    }

    public function test_unknown_product_is_a_404(): void
    {
        $this->get('/products/nope')->assertNotFound();
    }

    public function test_homepage_and_sitemap_include_products(): void
    {
        $this->get('/')->assertOk()->assertSee('Products I Built &amp; Own', false)->assertSee('More coming soon');

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString(route('products.show', 'cuelara'), $xml);
        $this->assertStringContainsString(route('products.show', 'invoiceinspect'), $xml);
    }
}
