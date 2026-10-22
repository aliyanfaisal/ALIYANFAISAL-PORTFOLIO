<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Comments\Pages\ListComments;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CommentResourceTest extends TestCase
{
    use RefreshDatabase;

    private function comment(string $status = 'pending'): Comment
    {
        $post = BlogPost::create(['title' => 'P', 'slug' => 'p', 'body' => 'b', 'published_at' => now()->subDay()]);

        return $post->comments()->create(['name' => 'A', 'email' => 'a@example.com', 'body' => 'Hi', 'status' => $status]);
    }

    private function actingAsAdmin(): void
    {
        $this->actingAs(User::factory()->create());
    }

    public function test_admin_can_approve_reject_and_delete(): void
    {
        $this->actingAsAdmin();
        $comment = $this->comment();

        Livewire::test(ListComments::class)
            ->assertCanSeeTableRecords([$comment])
            ->callTableAction('approve', $comment);
        $this->assertSame('approved', $comment->fresh()->status);

        Livewire::test(ListComments::class)->callTableAction('reject', $comment);
        $this->assertSame('rejected', $comment->fresh()->status);

        Livewire::test(ListComments::class)->callTableAction('delete', $comment);
        $this->assertModelMissing($comment);
    }

    public function test_admin_can_switch_approval_mode(): void
    {
        $this->actingAsAdmin();

        Livewire::test(ListComments::class)
            ->callAction('approvalMode', ['mode' => 'admin']);
        $this->assertFalse(Setting::current()->auto_approve_comments);

        Livewire::test(ListComments::class)
            ->callAction('approvalMode', ['mode' => 'auto']);
        $this->assertTrue(Setting::current()->auto_approve_comments);
    }
}
