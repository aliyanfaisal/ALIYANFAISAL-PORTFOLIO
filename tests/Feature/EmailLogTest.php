<?php

namespace Tests\Feature;

use App\Mail\NewsletterOtpMail;
use App\Models\EmailLog;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_sent_emails_are_logged_without_their_body(): void
    {
        Mail::to('reader@example.com')->send(new NewsletterOtpMail('123456'));

        $log = EmailLog::first();
        $this->assertSame('reader@example.com', $log->to);
        $this->assertSame('sent', $log->status);
        $this->assertStringContainsString('123456', $log->subject);
        $this->assertArrayNotHasKey('body', $log->getAttributes());
    }

    public function test_nothing_is_logged_when_logging_is_off(): void
    {
        Setting::current()->update(['email_logging_enabled' => false]);

        Mail::to('reader@example.com')->send(new NewsletterOtpMail('123456'));

        $this->assertDatabaseCount('email_logs', 0);
    }

    public function test_an_unconfirmed_send_shows_as_failed_after_a_while(): void
    {
        $log = EmailLog::create(['to' => 'a@example.com', 'subject' => 's', 'status' => 'sending']);
        $this->assertSame('sending', $log->displayStatus());

        $this->travel(11)->minutes();
        $this->assertSame('failed', $log->fresh()->displayStatus());
    }

    public function test_the_admin_page_loads_and_can_toggle_logging(): void
    {
        $admin = \App\Models\User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin/email-logs')->assertOk();

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Filament\Resources\EmailLogs\Pages\ListEmailLogs::class)
            ->callAction('toggleLogging');

        $this->assertFalse(Setting::current()->email_logging_enabled);
    }
}
