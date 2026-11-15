<?php

namespace App\Jobs;

use App\Models\BlogPost;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PushBlogPostToCuelara implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 30;

    public function __construct(public BlogPost $post, public bool $force = false) {}

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60];
    }

    public function handle(): void
    {
        if (! $this->force && ! Setting::current()->cuelara_enabled) {
            return;
        }

        $url = config('services.cuelara.url');
        $token = config('services.cuelara.token');

        if (! $url || ! $token) {
            Log::warning('Cuelara push skipped: CUELARA_API_URL or CUELARA_API_TOKEN is not configured.', [
                'post_id' => $this->post->id,
            ]);

            return;
        }

        $post = $this->post->fresh(['categories', 'tags']);

        if ($post === null || (! $this->force && $post->cuelara_synced_at !== null)) {
            return;
        }

        Http::withToken($token)
            ->acceptJson()
            ->timeout(20)
            ->withHeaders(['Idempotency-Key' => 'blog-post-'.$post->id])
            ->post($url, $this->payload($post))
            ->throw();

        $post->forceFill(['cuelara_synced_at' => now()])->save();
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Cuelara push failed permanently.', [
            'post_id' => $this->post->id,
            'message' => $exception->getMessage(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(BlogPost $post): array
    {
        return [
            'external_id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'body' => $post->body,
            'status' => $post->published_at === null ? 'draft' : 'published',
            'image_url' => $post->image_path ? asset('storage/'.$post->image_path) : null,
            'source_image_url' => $post->source_image_url,
            'categories' => $post->categories->pluck('name')->all(),
            'tags' => $post->tags->pluck('name')->all(),
            'published_at' => $post->published_at?->toIso8601String(),
            'canonical_url' => url('/blog/'.$post->slug),
        ];
    }
}
