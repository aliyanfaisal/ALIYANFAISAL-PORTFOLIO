<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramService
{
    public function isConfigured(?Setting $settings = null): bool
    {
        $settings ??= Setting::current();

        return filled(config('services.telegram.bot_token')) && filled($settings->telegram_chat_id);
    }

    /**
     * Posts a blog post to the channel. Throws on any Telegram API error so the job can retry.
     */
    public function sendPost(BlogPost $post, ?Setting $settings = null): void
    {
        $settings ??= Setting::current();

        $this->sendMessage($this->formatPost($post, $settings), $settings);
    }

    public function sendMessage(string $html, ?Setting $settings = null): void
    {
        $settings ??= Setting::current();

        Http::asJson()
            ->timeout(15)
            ->post('https://api.telegram.org/bot'.config('services.telegram.bot_token').'/sendMessage', [
                'chat_id' => $settings->telegram_chat_id,
                'text' => $html,
                'parse_mode' => 'HTML',
            ])
            ->throw();
    }

    public function formatPost(BlogPost $post, Setting $settings): string
    {
        $parts = ['<b>'.e($post->title).'</b>'];

        if ($settings->telegram_include_excerpt && filled($post->excerpt)) {
            $parts[] = e(Str::limit($post->excerpt, 300));
        }

        $parts[] = route('blog.show', $post);

        if ($settings->telegram_include_hashtags) {
            $tags = $post->categories
                ->map(fn ($category) => '#'.preg_replace('/[^A-Za-z0-9_]/', '', Str::studly($category->name)))
                ->filter(fn (string $tag) => $tag !== '#')
                ->implode(' ');

            if ($tags !== '') {
                $parts[] = $tags;
            }
        }

        return implode("\n\n", $parts);
    }
}
