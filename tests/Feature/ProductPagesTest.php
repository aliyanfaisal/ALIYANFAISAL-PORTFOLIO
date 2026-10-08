<?php

namespace Tests\Feature;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    public function test_manajet_page_renders_demo_form_without_a_live_site_link(): void
    {
        $this->get('/products/manajet')
            ->assertOk()
            ->assertSee('Get a demo')
            ->assertSee('https://github.com/aliyanfaisal/ManaJet-Showcase');
    }

    public function test_demo_request_is_stored_and_emailed(): void
    {
        Mail::fake();
        config(['mail.contact_recipient' => 'me@example.test']);

        $this->post('/products/manajet/demo', [
            'name' => 'Sam', 'email' => 'sam@acme.test', 'company' => 'Acme', 'team_size' => '6-15', 'message' => 'Agency of 10.',
        ])->assertRedirect(route('products.show', 'manajet').'#demo');

        $stored = ContactMessage::firstOrFail();
        $this->assertSame('ManaJet demo request', $stored->subject);
        $this->assertStringContainsString('Company: Acme', $stored->message);
        Mail::assertSent(ContactMessageMail::class);
    }

    public function test_demo_request_validates_and_ignores_honeypot_spam(): void
    {
        Mail::fake();

        $this->post('/products/manajet/demo', ['name' => '', 'email' => 'nope'])->assertSessionHasErrors(['name', 'email']);

        $this->post('/products/manajet/demo', ['name' => 'Bot', 'email' => 'bot@spam.test', 'website' => 'http://spam.test']);
        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingSent();
    }
}
