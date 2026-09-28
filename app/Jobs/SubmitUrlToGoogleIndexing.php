<?php

namespace App\Jobs;

use App\Models\BlogPost;
use App\Services\GoogleIndexingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubmitUrlToGoogleIndexing implements ShouldQueue
{
    use Queueable;

    public int $tries = 7;

    public int $timeout = 30;

    public function __construct(public string $slug, public string $type = GoogleIndexingService::URL_UPDATED) {}

    /**
     * Google's Indexing API enforces a daily quota that resets at US Pacific
     * midnight. A failure here is usually 429 quota exhaustion rather than a
     * real error, so retries are spread out over ~29 hours to guarantee at
     * least one attempt lands after the next quota reset.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [300, 1800, 3600, 10800, 21600, 64800];
    }

    public function handle(GoogleIndexingService $indexing): void
    {
        if (! $indexing->isConfigured()) {
            Log::warning('Google indexing skipped: credentials file is missing or unreadable.', ['slug' => $this->slug]);

            return;
        }

        if ($this->type === GoogleIndexingService::URL_UPDATED
            && ! BlogPost::published()->where('slug', $this->slug)->exists()) {
            Log::info('Google indexing skipped: post is not published.', ['slug' => $this->slug]);

            return;
        }

        $indexing->notify(url('/blog/'.$this->slug), $this->type);

        Log::info('Google indexing notification sent.', ['slug' => $this->slug, 'type' => $this->type]);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Google indexing notification failed permanently.', [
            'slug' => $this->slug,
            'type' => $this->type,
            'message' => $exception->getMessage(),
        ]);
    }
}
