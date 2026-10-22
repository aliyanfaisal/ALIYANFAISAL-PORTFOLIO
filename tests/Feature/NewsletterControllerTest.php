<?php

namespace Tests\Feature;

use App\Mail\NewsletterOtpMail;
use App\Mail\NewsletterWelcomeMail;
use App\Models\Category;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_sends_a_code_and_does_not_subscribe_until_confirmed(): void
    {
        Mail::fake();

        $this->post('/newsletter', ['email' => 'reader@example.com'])
            ->assertRedirect()
            ->assertSessionHas('newsletter_pending_email', 'reader@example.com');

        Mail::assertSent(NewsletterOtpMail::class);
        $this->assertFalse(NewsletterSubscriber::first()->isVerified());
        $this->assertSame(0, NewsletterSubscriber::active()->count());
    }

    public function test_the_correct_code_activates_the_subscription_and_sends_a_welcome(): void
    {
        Mail::fake();

        $this->post('/newsletter', ['email' => 'reader@example.com']);
        $subscriber = NewsletterSubscriber::first();
        $code = null;
        Mail::assertSent(NewsletterOtpMail::class, function ($mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $this->post('/newsletter/verify', ['email' => 'reader@example.com', 'code' => $code])->assertSessionHas('status');

        $this->assertTrue($subscriber->fresh()->isVerified());
        $this->assertSame(1, NewsletterSubscriber::active()->count());
        Mail::assertQueued(NewsletterWelcomeMail::class);
    }

    public function test_a_wrong_code_is_rejected_and_burns_after_five_tries(): void
    {
        Mail::fake();
        $this->post('/newsletter', ['email' => 'reader@example.com']);
        $subscriber = NewsletterSubscriber::first();
        $code = null;
        Mail::assertSent(NewsletterOtpMail::class, function ($mail) use (&$code) {
            $code = $mail->code;

            return true;
        });
        $wrong = $code === '000000' ? '111111' : '000000';

        foreach (range(1, 5) as $_) {
            $this->post('/newsletter/verify', ['email' => 'reader@example.com', 'code' => $wrong])->assertSessionHasErrors('code', null, 'newsletter');
        }

        $this->post('/newsletter/verify', ['email' => 'reader@example.com', 'code' => $code])->assertSessionHasErrors('code', null, 'newsletter');
        $this->assertFalse($subscriber->fresh()->isVerified());
    }

    public function test_an_expired_code_is_rejected(): void
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'reader@example.com']);
        $code = $subscriber->issueOtp();
        $this->travel(11)->minutes();

        $this->assertFalse($subscriber->fresh()->attemptOtp($code));
    }

    public function test_it_validates_the_email(): void
    {
        $this->post('/newsletter', ['email' => 'not-an-email'])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('newsletter_subscribers', 0);
    }

    public function test_category_preferences_are_saved_and_cleared(): void
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'a@example.com', 'verified_at' => now()]);
        $category = Category::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $this->put(route('newsletter.preferences.update', $subscriber->token), [
            'frequency' => 'weekly', 'topics' => 'selected', 'categories' => [$category->id],
        ]);
        $this->assertSame([$category->id], $subscriber->categories()->pluck('categories.id')->all());

        $this->put(route('newsletter.preferences.update', $subscriber->token), ['frequency' => 'weekly', 'topics' => 'all']);
        $this->assertSame(0, $subscriber->categories()->count());
    }

    public function test_the_post_page_shows_the_newsletter_and_channel_section_under_the_author_box(): void
    {
        \App\Models\Setting::current()->update(['telegram_channel_url' => 'https://t.me/chan', 'whatsapp_channel_url' => 'https://whatsapp.com/channel/x']);
        \App\Models\BlogPost::create(['title' => 'Hi', 'slug' => 'hi', 'excerpt' => 'e', 'body' => 'x', 'status' => 'published']);

        $this->get('/blog/hi')
            ->assertOk()
            ->assertSeeInOrder(['About the author', 'id="newsletter-post"', 'https://t.me/chan', 'https://whatsapp.com/channel/x'], false);
    }

    public function test_the_code_box_appears_only_in_the_form_that_was_used(): void
    {
        \App\Models\BlogPost::create(['title' => 'Hi', 'slug' => 'hi', 'excerpt' => 'e', 'body' => 'x', 'status' => 'published']);

        $this->withSession(['newsletter_pending_email' => 'a@example.com', 'newsletter_form' => 'newsletter-post'])
            ->get('/blog/hi')
            ->assertSee('id="newsletter-post-code"', false)
            ->assertDontSee('id="newsletter-code"', false);
    }
}
