<?php

namespace App\Models;

use App\Jobs\PushBlogPostToCuelara;
use App\Jobs\SubmitUrlToGoogleIndexing;
use App\Services\BlogPostFaqFormatter;
use App\Services\GoogleIndexingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'image_path', 'source_image_url', 'published_at',
        'status', 'cuelara_synced_at',
    ];

    /**
     * @var array{body: string, result: array{html: string, faqs: array<int, array{question: string, answer: string}>}}|null
     */
    private ?array $formattedBodyCache = null;

    protected $casts = [
        'published_at' => 'datetime',
        'cuelara_synced_at' => 'datetime',
    ];

    /**
     * Attributes whose change should be reported to Google for re-crawling.
     *
     * @var array<int, string>
     */
    private const INDEXABLE_ATTRIBUTES = ['title', 'slug', 'excerpt', 'body', 'image_path', 'published_at'];

    protected static function booted(): void
    {
        // `status` mirrors `published_at` rather than being independently editable, so every
        // save path (the API, Filament, factories) keeps a single source of truth for it.
        static::saving(function (BlogPost $post): void {
            $post->status = ($post->published_at !== null && $post->published_at->lessThanOrEqualTo(now()))
                ? 'published'
                : 'draft';
        });

        static::created(function (BlogPost $post): void {
            $post->notifyGoogleOfChange();

            // A brand-new post reclaiming a slug that used to redirect elsewhere should render
            // normally, not get bounced away by a stale redirect from whatever was deleted before it.
            BlogPostRedirect::where('from_slug', $post->slug)->delete();
        });

        static::updated(function (BlogPost $post): void {
            if ($post->wasChanged(self::INDEXABLE_ATTRIBUTES)) {
                $post->notifyGoogleOfChange();
            }

            // Only sync to Cuelara the moment a post actually flips from draft to published —
            // never on an ordinary edit to an already-published or still-draft post.
            if ($post->wasChanged('status') && $post->status === 'published') {
                PushBlogPostToCuelara::dispatch($post)->afterCommit();
            }
        });

        // Runs before the cascade-deletes the category/tag pivot rows, so the post's own
        // categories are still readable when picking the redirect's destination.
        static::deleting(function (BlogPost $post): void {
            if ($post->published_at === null) {
                return;
            }

            $category = $post->categories()->first();
            $destination = $category ? route('blog.category', $category) : route('blog.index');

            BlogPostRedirect::updateOrCreate(['from_slug' => $post->slug], ['to_url' => $destination]);
        });

        static::deleted(function (BlogPost $post): void {
            if ($post->published_at !== null && app(GoogleIndexingService::class)->isConfigured()) {
                SubmitUrlToGoogleIndexing::dispatch($post->slug, GoogleIndexingService::URL_DELETED)->afterCommit();
            }
        });
    }

    private function notifyGoogleOfChange(): void
    {
        if ($this->published_at === null || ! app(GoogleIndexingService::class)->isConfigured()) {
            return;
        }

        SubmitUrlToGoogleIndexing::dispatch($this->slug)
            ->delay($this->published_at->isFuture() ? $this->published_at : null)
            ->afterCommit();
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * The post body converted from Markdown to safe HTML.
     */
    protected function bodyHtml(): Attribute
    {
        return Attribute::get(fn (): string => $this->formattedBody()['html']);
    }

    /**
     * Question/answer pairs found under the post's "Frequently Asked Questions" heading.
     *
     * @return array<int, array{question: string, answer: string}>
     */
    protected function faqs(): Attribute
    {
        return Attribute::get(fn (): array => $this->formattedBody()['faqs']);
    }

    /**
     * @return array{html: string, faqs: array<int, array{question: string, answer: string}>}
     */
    private function formattedBody(): array
    {
        if ($this->formattedBodyCache !== null && $this->formattedBodyCache['body'] === $this->body) {
            return $this->formattedBodyCache['result'];
        }

        $html = Str::markdown($this->body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $result = app(BlogPostFaqFormatter::class)->format($html);
        $this->formattedBodyCache = ['body' => $this->body, 'result' => $result];

        return $result;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
