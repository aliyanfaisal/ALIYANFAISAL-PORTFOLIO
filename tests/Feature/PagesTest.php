<?php

namespace Tests\Feature;

use Tests\TestCase;

class PagesTest extends TestCase
{
    public function test_every_page_renders(): void
    {
        $urls = ['/', '/research', '/projects', '/open-source', '/reports', '/cv', '/contact',
            '/projects/cuelara', '/projects/invoiceinspect', '/projects/manajet', '/projects/gov-dashboard',
            '/open-source/notemind', '/sitemap.xml'];

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_unknown_pages_are_404(): void
    {
        $this->get('/projects/nope')->assertNotFound();
        $this->get('/open-source/nope')->assertNotFound();
        $this->get('/cv.pdf')->assertOk(); // the CV PDF is shipped in public/cv
    }

    public function test_home_has_the_profile_education_and_coming_soon(): void
    {
        $this->get('/')
            ->assertSee('Aliyan Faisal')
            ->assertSee('CGPA 3.80')
            ->assertSee('HEC Ehsaas Scholarship')
            ->assertSee('Master', false)
            ->assertSee('Uncertainty, calibration and abstention')
            ->assertDontSee('Structured output and constrained generation')
            ->assertDontSee('Misinformation and fact-checking')
            ->assertSee('Notemind')
            ->assertDontSee('More projects will be added here.')
            ->assertDontSee('More open-source work is coming.');
    }

    public function test_invoiceinspect_shows_its_evaluation_results(): void
    {
        $this->get('/projects/invoiceinspect')
            ->assertSee('84.5% field accuracy')
            ->assertSee('Corpus D')
            ->assertSee('contract-matching module')
            ->assertDontSee('99.5%')
            ->assertDontSee('93%');

        $this->get('/reports')->assertSee('InvoiceInspect: research report and evaluation harness reports')->assertSee('84.5% field accuracy');
    }

    public function test_retrieval_augmented_generation_is_not_claimed_for_invoiceinspect_as_a_current_feature(): void
    {
        $html = $this->get('/projects/invoiceinspect')->getContent();
        $summaryAndApproach = explode('Planned', $html)[0];

        $this->assertStringNotContainsString('retrieval-augmented', strtolower($summaryAndApproach));
        $this->assertStringNotContainsString('vector', strtolower($summaryAndApproach));
    }

    public function test_notemind_page_has_install_commands_and_repo(): void
    {
        $this->get('/open-source/notemind')
            ->assertSee('claude plugin marketplace add aliyanfaisal/notemind')
            ->assertSee('https://github.com/aliyanfaisal/notemind', false)
            ->assertSee('/notemind:note-add');
    }

    public function test_the_site_has_no_sales_language_and_no_links_to_other_sites(): void
    {
        foreach (['/', '/projects', '/cv', '/contact', '/projects/cuelara'] as $url) {
            $html = strtolower($this->get($url)->getContent());

            foreach (['hire me', 'fiverr', 'upwork', 'freelance.aliyanfaisal.com', 'href="https://aliyanfaisal.com'] as $needle) {
                $this->assertStringNotContainsString($needle, $html, "{$url} contains {$needle}");
            }
        }
    }

    public function test_pages_have_canonical_urls_and_person_schema(): void
    {
        $html = $this->get('/research')->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="'.url('/research').'">', $html);
        $this->assertStringContainsString('"@type":"Person"', $html);
        $this->assertStringContainsString('<title>Research interests — Aliyan Faisal</title>', $html);
    }
}
