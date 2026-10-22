<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/blog-posts')->assertRedirect('/admin/login');
    }

    public function test_non_admin_users_cannot_access_the_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_users_can_access_the_panel_pages(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin);

        $this->get('/admin')->assertOk();
        $this->get('/admin/blog-posts')->assertOk();
        $this->get('/admin/blog-posts/create')->assertOk();
        $this->get('/admin/categories')->assertOk();
        $this->get('/admin/categories/create')->assertOk();
        $this->get('/admin/tags')->assertOk();
        $this->get('/admin/tags/create')->assertOk();
        $this->get('/admin/settings')->assertOk();
        $this->get('/admin/newsletter-subscribers')->assertOk();
        $this->get('/admin/newsletters')->assertOk();
        $this->get('/admin/automation-logs')->assertOk();
    }
}
