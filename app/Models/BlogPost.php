<?php

namespace App\Models;

use App\Jobs\PushBlogPostToCuelara;
use App\Jobs\SubmitUrlToGoogleIndexing;
use App\Services\BlogPostContentEnhancer;
use App\Services\BlogPostFaqFormatter;
use App\Services\GoogleIndexingService;
use App\Services\ImageOptimizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'image_path', 'image_width', 'image_height', 'source_image_url', 'published_at',
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
        static::saving(function (BlogPost $post): void {
            $post->reconcileStatusAndPublishDate();
            $post->optimizeUploadedImage();
        });

        static::created(function (BlogPost $post): void {
            $post->notifyGoogleOfChange();

            // A brand-new post reclaiming a slug that used to redirect elsewhere should render
            // normally, not get bounced away by a stale redirect from whatever was deleted before it.
            BlogPostRedirect::where('from_slug', $post->slug)->delete();
        });

        static::updated(function (BlogPost $post): void {
            // A renamed slug 301s to the new URL so inbound links and indexed URLs never 404.
            if ($post->wasChanged('slug')) {
                $post->redirectOldSlug($post->getOriginal('slug'));
            }

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

    /**
     * Keeps `status` and `published_at` consistent on every save path (Filament, API, scheduler).
     *
     * - Published with no date, or flipped to published while still holding a future date, goes live now.
     * - A future date the editor just entered is a schedule: the post stays a draft until that time.
     * - A draft never keeps a past date; only a future one, which the scheduler later publishes.
     * - A post saved with no explicit status, or with only its date changed, takes the status its date implies.
     */
    private function reconcileStatusAndPublishDate(): void
    {
        $dateAlone = $this->isDirty('published_at') && ! $this->isDirty('status');
        $status = ($this->status === null || $dateAlone)
            ? (($this->published_at !== null && $this->published_at->lessThanOrEqualTo(now())) ? 'published' : 'draft')
            : $this->status;

        if ($status === 'published') {
            if ($this->published_at === null) {
                $this->published_at = now();
            } elseif ($this->published_at->isFuture()) {
                if ($this->isDirty('published_at')) {
                    $status = 'draft';
                } else {
                    $this->published_at = now();
                }
            }
        } elseif ($this->published_at !== null && ! $this->published_at->isFuture()) {
            $this->published_at = null;
        }

        $this->status = $status;
    }

    /**
     * Images set outside the API (e.g. a Filament upload) get the same 1600x900 WebP treatment.
     */
    private function optimizeUploadedImage(): void
    {
        if ($this->image_path === null || ! $this->isDirty('image_path')) {
            return;
        }

        $disk = Storage::disk('public');

        if (str_ends_with($this->image_path, '.webp') && $this->image_width !== null) {
            return;
        }

        try {
            $result = app(ImageOptimizer::class)->optimize($disk->get($this->image_path));
        } catch (Throwable $exception) {
            report($exception);

            return;
        }

        $path = "blog/{$this->slug}.webp";
        $disk->put($path, $result['contents']);

        if ($path !== $this->image_path) {
            $disk->delete($this->image_path);
        }

        $this->image_path = $path;
        $this->image_width = $result['width'];
        $this->image_height = $result['height'];
    }

    private function redirectOldSlug(string $oldSlug): void
    {
        BlogPostRedirect::updateOrCreate(['from_slug' => $oldSlug], ['to_url' => route('blog.show', $this)]);

        // The new slug is live now, so it must not keep a stale redirect of its own.
        BlogPostRedirect::where('from_slug', $this->slug)->delete();

        // Redirects that pointed at the old URL would otherwise chain through it.
        BlogPostRedirect::where('to_url', route('blog.show', ['slug' => $oldSlug]))->update(['to_url' => route('blog.show', $this)]);
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
     * h2/h3 headings (id, text, level) for the table of contents.
     *
     * @return array<int, array{id: string, text: string, level: int}>
     */
    protected function headings(): Attribute
    {
        return Attribute::get(fn (): array => $this->formattedBody()['headings']);
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
     * @return array{html: string, faqs: array<int, array{question: string, answer: string}>, headings: array<int, array{id: string, text: string, level: int}>}
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
        $enhanced = app(BlogPostContentEnhancer::class)->enhance($result['html'], $this->title ?? '');
        $result = ['html' => $enhanced['html'], 'faqs' => $result['faqs'], 'headings' => $enhanced['headings']];
        $this->formattedBodyCache = ['body' => $this->body, 'result' => $result];

        return $result;
    }

    /**
     * The hero image URL, or null when the post has none.
     */
    public function imageUrl(): ?string
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    /**
     * Other published posts, ranked by how many categories and tags they share with this one.
     *
     * @return Collection<int, BlogPost>
     */
    public function relatedPosts(int $limit = 4): Collection
    {
        $categoryIds = $this->categories->pluck('id');
        $tagIds = $this->tags->pluck('id');

        if ($categoryIds->isEmpty() && $tagIds->isEmpty()) {
            return static::published()->whereKeyNot($this->id)->with('categories')->latest('published_at')->limit($limit)->get();
        }

        return static::published()
            ->whereKeyNot($this->id)
            ->where(fn (Builder $query) => $query
                ->whereHas('categories', fn (Builder $q) => $q->whereIn('categories.id', $categoryIds))
                ->orWhereHas('tags', fn (Builder $q) => $q->whereIn('tags.id', $tagIds)))
            ->with(['categories', 'tags:id'])
            ->latest('published_at')
            ->limit(40)
            ->get()
            ->sortByDesc(fn (BlogPost $post): int => $post->categories->pluck('id')->intersect($categoryIds)->count() * 3
                + $post->tags->pluck('id')->intersect($tagIds)->count())
            ->take($limit)
            ->values();
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
