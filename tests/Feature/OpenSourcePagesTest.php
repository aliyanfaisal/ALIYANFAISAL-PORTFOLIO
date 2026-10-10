<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OpenSourcePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_notemind(): void
    {
        $this->get('/open-source')
            ->assertOk()
            ->assertSee('Open Source I Build')
            ->assertSee('Notemind')
            ->assertSee(route('open-source.show', 'notemind'), false);
    }

    public function test_notemind_details_page_has_install_commands_repo_and_schema(): void
    {
        $html = $this->get('/open-source/notemind')
            ->assertOk()
            ->assertSee('claude plugin marketplace add aliyanfaisal/notemind')
            ->assertSee('claude plugin install notemind@notemind')
            ->assertSee('https://github.com/aliyanfaisal/notemind', false)
            ->assertSee('/notemind:note-add')
            ->getContent();

        $this->assertStringContainsString('"SoftwareSourceCode"', $html);
        $this->assertStringContainsString('https://opensource.org/licenses/MIT', $html);
    }

    public function test_unknown_project_is_a_404(): void
    {
        $this->get('/open-source/nope')->assertNotFound();
    }

    public function test_homepage_and_sitemap_include_open_source(): void
    {
        $this->get('/')->assertOk()->assertSee('Open Source I Build');

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString(route('open-source.index'), $xml);
        $this->assertStringContainsString(route('open-source.show', 'notemind'), $xml);
    }
}
