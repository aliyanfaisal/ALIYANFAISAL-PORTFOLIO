<?php

namespace Tests\Feature\Api;

use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.blog_api.token' => 'test-token']);
    }

    private function headers(): array
    {
        return ['Authorization' => 'Bearer test-token'];
    }

    public function test_it_rejects_requests_without_a_valid_token(): void
    {
        Category::create(['name' => 'X', 'slug' => 'x']);

        $this->getJson('/api/categories')->assertStatus(401);
        $this->postJson('/api/categories', ['name' => 'X'])->assertStatus(401);
        $this->patchJson('/api/categories/x', ['description' => 'y'])->assertStatus(401);
    }

    public function test_it_lists_categories_with_descriptions_and_published_post_counts(): void
    {
        $category = Category::create(['name' => 'RAG', 'slug' => 'rag', 'description' => 'About RAG.']);
        Category::create(['name' => 'Automation', 'slug' => 'automation']);
        BlogPost::create(['title' => 'A', 'slug' => 'a', 'body' => 'x', 'published_at' => now()->subDay()])->categories()->attach($category);
        BlogPost::create(['title' => 'B', 'slug' => 'b', 'body' => 'x', 'status' => 'draft'])->categories()->attach($category);

        $this->getJson('/api/categories', $this->headers())
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'automation')
            ->assertJsonPath('data.0.description', null)
            ->assertJsonPath('data.1.slug', 'rag')
            ->assertJsonPath('data.1.description', 'About RAG.')
            ->assertJsonPath('data.1.published_posts_count', 1)
            ->assertJsonPath('data.1.url', route('blog.category', $category));
    }

    public function test_it_shows_one_category_by_slug(): void
    {
        Category::create(['name' => 'RAG', 'slug' => 'rag']);

        $this->getJson('/api/categories/rag', $this->headers())->assertOk()->assertJsonPath('name', 'RAG');
        $this->getJson('/api/categories/missing', $this->headers())->assertNotFound();
    }

    public function test_it_creates_a_category_and_derives_the_slug(): void
    {
        $this->postJson('/api/categories', ['name' => 'LLM Integration', 'description' => 'Intro.'], $this->headers())
            ->assertStatus(201)
            ->assertJsonPath('slug', 'llm-integration')
            ->assertJsonPath('description', 'Intro.');

        $this->assertDatabaseHas('categories', ['slug' => 'llm-integration']);
    }

    public function test_it_rejects_a_duplicate_category(): void
    {
        Category::create(['name' => 'RAG', 'slug' => 'rag']);

        $this->postJson('/api/categories', ['name' => 'rag'], $this->headers())
            ->assertStatus(422)->assertJsonValidationErrors(['slug']);
        $this->postJson('/api/categories', [], $this->headers())
            ->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    public function test_it_updates_description_and_name_but_never_the_slug(): void
    {
        Category::create(['name' => 'RAG', 'slug' => 'rag']);

        $this->patchJson('/api/categories/rag', ['description' => 'New intro.', 'slug' => 'changed'], $this->headers())
            ->assertOk()
            ->assertJsonPath('slug', 'rag')
            ->assertJsonPath('description', 'New intro.');

        $this->assertDatabaseHas('categories', ['slug' => 'rag', 'description' => 'New intro.']);
        $this->putJson('/api/categories/rag', ['description' => str_repeat('a', 301)], $this->headers())
            ->assertStatus(422)->assertJsonValidationErrors(['description']);
    }
}
