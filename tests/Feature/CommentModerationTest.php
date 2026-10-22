<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\Setting;
use App\Rules\SafeComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CommentModerationTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(): BlogPost
    {
        return BlogPost::create([
            'title' => 'Published Post', 'slug' => 'published-post', 'body' => 'Body',
            'published_at' => now()->subDay(),
        ]);
    }

    private function submit(BlogPost $post, string $body)
    {
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

        return $this->post(route('blog.comments.store', $post->slug), [
            'name' => 'Jane Doe', 'email' => 'jane@example.com', 'body' => $body,
            'cf-turnstile-response' => 'token',
        ]);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function badComments(): array
    {
        return [
            'script tag' => ['Nice <script>alert(1)</script>'],
            'img onerror' => ['<img src=x onerror=alert(1)>'],
            'javascript uri' => ['click javascript:alert(1)'],
            'encoded tag' => ['&lt;script&gt;x&lt;/script&gt;'],
            'sql injection' => ["1' OR 1=1; -- drop table users"],
            'union select' => ['x UNION SELECT password FROM users'],
            'link' => ['see https://spam.example/offer'],
            'bare domain' => ['visit my-shop.com today'],
            'obfuscated domain' => ['visit myshop dot com now'],
            'email' => ['write to bob at gmail dot com'],
            'phone' => ['call +92 300 1234567'],
            'abuse' => ['you are an asshole'],
            'leet abuse' => ['what a sh1t post'],
            'spaced abuse' => ['f u c k this'],
            'stretched abuse' => ['fuuuuck off'],
            'spam phrase' => ['Best casino bonus, buy now'],
            'shouting' => ['THIS IS THE BEST POST EVER READ IT'],
            'repeat chars' => ['coooooooooool post'],
            'repeat text' => ['great post great post great post'],
            'zero width' => ["hi\u{200B}there"],
        ];
    }

    #[DataProvider('badComments')]
    public function test_rule_rejects_malicious_or_spammy_text(string $body): void
    {
        $validator = Validator::make(['body' => $body], ['body' => [new SafeComment]]);

        $this->assertTrue($validator->fails(), "Should reject: {$body}");
    }

    /**
     * @return array<string, array{string}>
     */
    public static function goodComments(): array
    {
        return [
            ['Great article, thanks for the detailed walkthrough of caching!'],
            ['I think the second approach is better. It scales, and the code is easier to read.'],
            ['Could you cover Laravel queues next? Select the best option from the docs, I guess.'],
            ["Loved it — the section on N+1 queries saved me hours (I'd missed it before)."],
            ['Café-style naïve résumé tips are useful too.'],
        ];
    }

    #[DataProvider('goodComments')]
    public function test_rule_allows_normal_comments(string $body): void
    {
        $validator = Validator::make(['body' => $body], ['body' => [new SafeComment]]);

        $this->assertFalse($validator->fails(), implode(' ', $validator->errors()->all()));
    }

    public function test_blocked_comment_is_not_saved_and_shows_error(): void
    {
        $response = $this->submit($this->makePost(), '<script>alert(1)</script>');

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comments_are_auto_approved_by_default(): void
    {
        $post = $this->makePost();
        $this->submit($post, 'Great article!');

        $this->assertDatabaseHas('comments', ['body' => 'Great article!', 'status' => 'approved']);
        $this->get(route('blog.show', $post))->assertSee('Great article!');
    }

    public function test_admin_approval_mode_holds_comments_until_approved(): void
    {
        Setting::current()->update(['auto_approve_comments' => false]);
        $post = $this->makePost();

        $this->submit($post, 'Awaiting review')->assertSessionHas('status');

        $comment = Comment::firstOrFail();
        $this->assertSame('pending', $comment->status);
        $this->get(route('blog.show', $post))->assertDontSee('Awaiting review');

        $comment->update(['status' => Comment::STATUS_APPROVED]);
        $this->get(route('blog.show', $post))->assertSee('Awaiting review');
    }

    public function test_rejected_comments_are_hidden(): void
    {
        $post = $this->makePost();
        $post->comments()->create(['name' => 'A', 'email' => 'a@example.com', 'body' => 'Hidden one', 'status' => 'rejected']);

        $this->get(route('blog.show', $post))->assertDontSee('Hidden one');
    }
}
