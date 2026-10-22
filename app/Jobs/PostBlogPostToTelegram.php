<?php

namespace App\Jobs;

use App\Models\BlogPost;
use App\Models\Setting;
use App\Services\TelegramService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class PostBlogPostToTelegram implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(public BlogPost $post, public bool $force = false) {}

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 300];
    }

    public function handle(TelegramService $telegram): void
    {
        $settings = Setting::current();
        $post = $this->post->fresh(['categories']);

        // The switch is re-checked here so turning it off also stops posts already waiting out a delay.
        if ($post === null || (! $this->force && (! $settings->telegram_enabled || $post->telegram_posted_at !== null))) {
            return;
        }

        if ($post->status !== 'published' || ! $telegram->isConfigured($settings)) {
            Log::warning('Telegram post skipped: post not published or Telegram not configured.', ['post_id' => $post->id]);

            return;
        }

        $telegram->sendPost($post, $settings);

        $post->forceFill(['telegram_posted_at' => now()])->saveQuietly();
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Telegram post failed permanently.', [
            'post_id' => $this->post->id,
            'message' => $exception->getMessage(),
        ]);
    }
}
