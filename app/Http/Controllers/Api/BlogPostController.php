<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\PushBlogPostToCuelara;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use App\Services\ImageOptimizer;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class BlogPostController extends Controller
{
    private const UPLOAD_DIRECTORY = 'generated';

    private const IMAGE_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('blog_posts', 'slug')],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'categories' => ['nullable', 'array'],
            'categories.*' => [$this->categoryRule(...)],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,published'],
            'send_to_cuelara' => ['nullable', 'boolean'],
        ]);

        $slug = $data['slug'] ?? $this->generateUniqueSlug($data['title']);

        $image = null;
        if (! empty($data['image_url'])) {
            $image = $this->storeOptimizedImage($data['image_url'], $slug);
        }

        $autoApprove = Setting::current()->auto_approve_posts;
        $wantsDraft = ($data['status'] ?? 'published') === 'draft';
        $publishNow = ! $wantsDraft && $autoApprove;

        $post = BlogPost::create([
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? $this->deriveExcerpt($data['body']),
            'body' => $data['body'],
            'image_path' => $image['path'] ?? null,
            'image_width' => $image['width'] ?? null,
            'image_height' => $image['height'] ?? null,
            'source_image_url' => $data['image_url'] ?? null,
            'status' => $publishNow ? 'published' : 'draft',
            'published_at' => $publishNow ? ($data['published_at'] ?? now()) : null,
        ]);

        $post->categories()->sync($this->resolveCategories($data['categories'] ?? []));
        $post->tags()->sync($this->resolveTerms(Tag::class, $data['tags'] ?? []));

        // A draft has nothing worth syncing yet — Cuelara only hears about a post once it's
        // actually published (here, or later when an edit flips it from draft to published).
        if ($post->status === 'published' && $request->boolean('send_to_cuelara', true)) {
            PushBlogPostToCuelara::dispatch($post)->afterResponse();
        }

        return response()->json([
            'id' => $post->id,
            'status' => $wantsDraft ? 'draft' : ($publishNow ? 'published' : 'pending_review'),
            'url' => url('/blog/'.$post->slug),
            'image_url' => $post->image_path ? asset('storage/'.$post->image_path) : null,
            'slug' => $post->slug,
            'categories' => $post->categories()->pluck('name'),
            'tags' => $post->tags()->pluck('name'),
        ], 201);
    }

    /**
     * List posts (draft and published alike) so a caller can check on what it's created.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:draft,published'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $posts = BlogPost::query()
            ->when($data['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest('id')
            ->paginate($data['per_page'] ?? 20);

        return response()->json([
            'data' => $posts->getCollection()->map($this->summarize(...))->all(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function show(BlogPost $blogPost): JsonResponse
    {
        return response()->json($this->present($blogPost->load(['categories', 'tags'])));
    }

    /**
     * Update a post's content and/or its draft/published state.
     *
     * Setting `status` to "published" (or supplying `published_at`) on a post that was
     * previously a draft is what actually flips it live — the model then takes care of syncing
     * it to Cuelara itself, exactly once, the moment that transition happens.
     */
    public function update(Request $request, BlogPost $blogPost): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['sometimes', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => [$this->categoryRule(...)],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:draft,published'],
        ]);

        if (array_key_exists('image_url', $data) && $data['image_url']) {
            $image = $this->storeOptimizedImage($data['image_url'], $blogPost->slug);
            $blogPost->image_path = $image['path'];
            $blogPost->image_width = $image['width'];
            $blogPost->image_height = $image['height'];
            $blogPost->source_image_url = $data['image_url'];
        }

        $blogPost->fill(collect($data)->only(['title', 'excerpt', 'body'])->all());

        if (($data['status'] ?? null) === 'draft' && ! array_key_exists('published_at', $data)) {
            $blogPost->published_at = null;
        } elseif (array_key_exists('published_at', $data)) {
            $blogPost->published_at = $data['published_at'];
        }

        if (isset($data['status'])) {
            $blogPost->status = $data['status'];
        } elseif (array_key_exists('published_at', $data)) {
            $blogPost->status = $data['published_at'] === null ? 'draft' : 'published';
        }

        $blogPost->save();

        if (array_key_exists('categories', $data)) {
            $blogPost->categories()->sync($this->resolveCategories($data['categories']));
        }

        if (array_key_exists('tags', $data)) {
            $blogPost->tags()->sync($this->resolveTerms(Tag::class, $data['tags']));
        }

        return response()->json($this->present($blogPost->fresh(['categories', 'tags'])));
    }

    public function destroy(BlogPost $blogPost): JsonResponse
    {
        $blogPost->delete();

        return response()->json(['status' => 'deleted', 'id' => $blogPost->id]);
    }

    /**
     * @return array<string, mixed>
     */
    private function present(BlogPost $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'status' => $post->status,
            'excerpt' => $post->excerpt,
            'body' => $post->body,
            'url' => url('/blog/'.$post->slug),
            'image_url' => $post->image_path ? asset('storage/'.$post->image_path) : null,
            'categories' => $post->categories->pluck('name'),
            'tags' => $post->tags->pluck('name'),
            'published_at' => $post->published_at?->toIso8601String(),
            'created_at' => $post->created_at?->toIso8601String(),
            'updated_at' => $post->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function summarize(BlogPost $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'status' => $post->status,
            'url' => url('/blog/'.$post->slug),
            'published_at' => $post->published_at?->toIso8601String(),
        ];
    }

    /**
     * List the image URLs of the most recent posts so new runs can avoid reusing them.
     */
    public function recentImages(): JsonResponse
    {
        $recentImages = BlogPost::query()
            ->latest()
            ->latest('id')
            ->limit(20)
            ->get(['slug', 'image_path', 'source_image_url'])
            ->map(fn (BlogPost $post): array => [
                'blog_url' => url('/blog/'.$post->slug),
                'source_image_url' => $post->source_image_url,
                'rehosted_image_url' => $post->image_path ? asset('storage/'.$post->image_path) : null,
            ]);

        return response()->json(['recent_images' => $recentImages]);
    }

    /**
     * A category is either a plain name or {"name": "...", "description": "..."}.
     */
    private function categoryRule(string $attribute, mixed $value, \Closure $fail): void
    {
        $name = is_array($value) ? ($value['name'] ?? null) : $value;
        $description = is_array($value) ? ($value['description'] ?? null) : null;

        if (! is_string($name) || trim($name) === '' || mb_strlen($name) > 100) {
            $fail('Each category must be a name (max 100 characters) or an object with a "name".');
        } elseif ($description !== null && (! is_string($description) || mb_strlen($description) > 300)) {
            $fail('A category description must be a string of at most 300 characters.');
        }
    }

    /**
     * Find or create each category and return their ids for syncing. A supplied description is only
     * written to a category that has none yet; use PATCH /api/categories/{slug} to overwrite one.
     *
     * @param  array<int, string|array{name: string, description?: string|null}>  $categories
     * @return array<int, int>
     */
    private function resolveCategories(array $categories): array
    {
        return collect($categories)
            ->map(fn ($category) => is_array($category)
                ? ['name' => trim($category['name']), 'description' => trim((string) ($category['description'] ?? ''))]
                : ['name' => trim($category), 'description' => ''])
            ->filter(fn (array $category) => $category['name'] !== '')
            ->unique('name')
            ->map(function (array $category): int {
                $model = Category::firstOrCreate(
                    ['slug' => Str::slug($category['name'])],
                    ['name' => $category['name'], 'description' => $category['description'] ?: null],
                );

                if ($category['description'] !== '' && blank($model->description)) {
                    $model->update(['description' => $category['description']]);
                }

                return $model->id;
            })
            ->values()
            ->all();
    }

    /**
     * Find or create each named term and return their ids for syncing.
     *
     * @param  class-string<Tag>  $model
     * @param  array<int, string>  $names
     * @return array<int, int>
     */
    private function resolveTerms(string $model, array $names): array
    {
        return collect($names)
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->map(fn (string $name) => $model::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            )->id)
            ->all();
    }

    /**
     * Derive a plain-text excerpt from Markdown body content.
     */
    private function deriveExcerpt(string $body): string
    {
        $html = Str::markdown($body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($html))), 160, '');
    }

    private function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 2;

        while (BlogPost::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * Return the storage path when the URL points to an image already uploaded
     * through the upload-image API, so it is reused instead of downloaded again.
     */
    private function resolveUploadedImagePath(string $url): ?string
    {
        $storageUrl = parse_url(asset('storage/'.self::UPLOAD_DIRECTORY.'/'));
        $imageUrl = parse_url($url);

        if (! isset($imageUrl['host'], $imageUrl['path'])
            || strcasecmp($imageUrl['host'], $storageUrl['host']) !== 0
            || ($imageUrl['port'] ?? null) !== ($storageUrl['port'] ?? null)
        ) {
            return null;
        }

        $path = rawurldecode($imageUrl['path']);
        $prefix = rtrim($storageUrl['path'], '/').'/';

        if (! str_starts_with($path, $prefix) || str_contains($path, '..')) {
            return null;
        }

        $relativePath = self::UPLOAD_DIRECTORY.'/'.substr($path, strlen($prefix));

        return Storage::disk('public')->exists($relativePath) ? $relativePath : null;
    }

    /**
     * Fetch the image (an earlier upload-image upload, or a remote URL), then crop it to 16:9, shrink it
     * to at most 1600x900 and store it as WebP so the hero image doesn't wreck LCP.
     *
     * @return array{path: string, width: int, height: int}
     */
    private function storeOptimizedImage(string $url, string $slug): array
    {
        $uploadedPath = $this->resolveUploadedImagePath($url);
        $contents = $uploadedPath !== null
            ? Storage::disk('public')->get($uploadedPath)
            : $this->downloadImage($url);

        try {
            $result = app(ImageOptimizer::class)->optimize($contents);
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'image_url' => 'The provided image could not be processed (jpg, png, gif or webp expected).',
            ]);
        }

        $path = "blog/{$slug}.webp";
        Storage::disk('public')->put($path, $result['contents']);

        return ['path' => $path, 'width' => $result['width'], 'height' => $result['height']];
    }

    private function downloadImage(string $url): string
    {
        try {
            $response = Http::timeout(15)->get($url);
        } catch (ConnectionException) {
            throw ValidationException::withMessages([
                'image_url' => 'Could not connect to the provided image URL.',
            ]);
        }

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'image_url' => "Failed to download image, remote server responded with status {$response->status()}.",
            ]);
        }

        $contentType = strtolower(explode(';', $response->header('Content-Type') ?? '')[0]);

        if (! isset(self::IMAGE_EXTENSIONS[$contentType])) {
            throw ValidationException::withMessages([
                'image_url' => 'The provided URL does not point to a supported image type (jpg, png, gif, webp).',
            ]);
        }

        return $response->body();
    }
}
