<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\PushBlogPostToCuelara;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            'categories.*' => ['string', 'max:100'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,published'],
            'send_to_cuelara' => ['nullable', 'boolean'],
        ]);

        $slug = $data['slug'] ?? $this->generateUniqueSlug($data['title']);

        $imagePath = null;
        if (! empty($data['image_url'])) {
            $imagePath = $this->resolveUploadedImagePath($data['image_url'])
                ?? $this->downloadImage($data['image_url'], $slug);
        }

        $autoApprove = Setting::current()->auto_approve_posts;
        $wantsDraft = ($data['status'] ?? 'published') === 'draft';
        $publishNow = ! $wantsDraft && $autoApprove;

        $post = BlogPost::create([
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? $this->deriveExcerpt($data['body']),
            'body' => $data['body'],
            'image_path' => $imagePath,
            'source_image_url' => $data['image_url'] ?? null,
            'status' => $publishNow ? 'published' : 'draft',
            'published_at' => $publishNow ? ($data['published_at'] ?? now()) : null,
        ]);

        $post->categories()->sync($this->resolveTerms(Category::class, $data['categories'] ?? []));
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
            'categories.*' => ['string', 'max:100'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:100'],
            'published_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:draft,published'],
        ]);

        if (array_key_exists('image_url', $data) && $data['image_url']) {
            $blogPost->image_path = $this->resolveUploadedImagePath($data['image_url'])
                ?? $this->downloadImage($data['image_url'], $blogPost->slug);
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
            $blogPost->categories()->sync($this->resolveTerms(Category::class, $data['categories']));
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
     * Find or create each named term and return their ids for syncing.
     *
     * @param  class-string<Category|Tag>  $model
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

    private function downloadImage(string $url, string $slug): string
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
        $extension = self::IMAGE_EXTENSIONS[$contentType] ?? null;

        if (! $extension) {
            throw ValidationException::withMessages([
                'image_url' => 'The provided URL does not point to a supported image type (jpg, png, gif, webp).',
            ]);
        }

        $path = "blog/{$slug}.{$extension}";

        Storage::disk('public')->put($path, $response->body());

        return $path;
    }
}
