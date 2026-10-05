@php
    use App\Support\Seo\Schema;
    use App\Support\Seo\Seo;

    $settings = \App\Models\Setting::current();
    $postUrl = route('blog.show', $post);
    $metaDescription = \Illuminate\Support\Str::limit(
        $post->excerpt ?: trim(preg_replace('/\s+/', ' ', strip_tags($post->body_html))),
        150,
        '…',
        true,
    );

    $imageWidth = $post->image_width ?: 1600;
    $imageHeight = $post->image_height ?: 900;
    if ($post->image_path && ! $post->image_width) {
        // Not yet converted by `blog:optimize-images`: read the real size rather than guessing.
        $imageSize = @getimagesize(storage_path('app/public/'.$post->image_path));
        if ($imageSize) {
            [$imageWidth, $imageHeight] = $imageSize;
        }
    }
    $seoImage = $post->image_path
        ? ['url' => $post->imageUrl(), 'width' => $imageWidth, 'height' => $imageHeight]
        : ($settings->default_og_image
            ? ['url' => asset('storage/'.$settings->default_og_image), 'width' => 1200, 'height' => 630]
            : Seo::defaultImage());

    $graph = Schema::blogPosting($post, $metaDescription, $seoImage);
    $category = $post->categories->first();
    $breadcrumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Blog', 'url' => route('blog.index')],
    ];
    if ($category) {
        $breadcrumbs[] = ['name' => $category->name, 'url' => route('blog.category', $category)];
    }

    $isUpdated = $post->updated_at && $post->updated_at->greaterThanOrEqualTo($post->published_at->copy()->addDay());
    $tocHeadings = collect($post->headings)->where('level', 2)->values();
    $showToc = $tocHeadings->count() >= 4;
    $related = $post->related ?? collect();
    $totalComments = $post->comments->sum(fn ($comment) => 1 + $comment->replies->count());
    $sessionId = session()->getId();
    // Cast to an object so an empty result serializes as {} rather than [] — a bare [] would
    // give the Alpine component's `counts` an array shape instead of the object one it expects.
    $postReactionCounts = (object) $post->reactions->groupBy('emoji')->map->count()->all();
    $myPostReaction = $post->reactions->firstWhere('session_id', $sessionId)?->emoji;
@endphp
<x-layouts.app :title="$post->title.' — '.$settings->site_name" :description="$metaDescription" :image="$seoImage" og-type="article" :graph="$graph">
    <x-slot:head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css" media="print" onload="this.media='all'">
        <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css"></noscript>

        <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
        <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
        <meta property="article:author" content="{{ route('about') }}">
        @if ($category)
            <meta property="article:section" content="{{ $category->name }}">
        @endif
        @foreach ($post->tags as $tag)
            <meta property="article:tag" content="{{ $tag->name }}">
        @endforeach

        <script>
            window.renderTurnstileWhenReady = function (el) {
                if (!el || el.dataset.turnstileRendered === '1') return;
                (function attempt() {
                    if (window.turnstile) {
                        window.turnstile.render(el, { sitekey: @js(config('services.turnstile.site_key')) });
                        el.dataset.turnstileRendered = '1';
                    } else {
                        setTimeout(attempt, 100);
                    }
                })();
            };
        </script>
    </x-slot:head>

    <article class="mx-auto max-w-4xl px-6 py-16">
        <nav aria-label="Breadcrumb" class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">
            <ol class="flex flex-wrap items-center gap-2">
                @foreach ($breadcrumbs as $crumb)
                    <li class="flex items-center gap-2">
                        <a href="{{ $crumb['url'] }}" class="transition hover:text-indigo-500 dark:hover:text-indigo-400">{{ $crumb['name'] }}</a>
                        <span aria-hidden="true" class="text-zinc-300 dark:text-zinc-700">/</span>
                    </li>
                @endforeach
                <li aria-current="page" class="truncate font-medium text-zinc-700 dark:text-zinc-300">{{ $post->title }}</li>
            </ol>
        </nav>

        @if ($post->categories->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($post->categories as $category)
                    <a href="{{ route('blog.category', $category) }}" class="rounded-full bg-indigo-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-500 transition hover:bg-indigo-500/20 dark:text-indigo-400">{{ $category->name }}</a>
                @endforeach
            </div>
        @endif

        <h1 class="mt-3 text-4xl font-bold text-zinc-900 dark:text-white">{{ $post->title }}</h1>

        <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-zinc-500 dark:text-zinc-400">
            <span>Published <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('F j, Y') }}</time></span>
            @if ($isUpdated)
                <span class="text-zinc-300 dark:text-zinc-700">&middot;</span>
                <span>Updated <time datetime="{{ $post->updated_at->toIso8601String() }}">{{ $post->updated_at->format('F j, Y') }}</time></span>
            @endif
            <span class="text-zinc-300 dark:text-zinc-700">&middot;</span>
            <span class="inline-flex items-center gap-1.5">
                <x-icon name="eye" class="size-4" />
                {{ number_format($post->views) }} views
            </span>
        </div>

        @if ($post->image_path)
            <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" width="{{ $imageWidth }}" height="{{ $imageHeight }}" fetchpriority="high" decoding="async" class="mt-8 h-auto w-full rounded-2xl">
        @endif

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <div
                x-data="{
                    counts: {{ \Illuminate\Support\Js::from($postReactionCounts) }},
                    mine: {{ \Illuminate\Support\Js::from($myPostReaction) }},
                    react(emoji) {
                        fetch({{ \Illuminate\Support\Js::from(route('blog.reactions.store', $post)) }}, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ emoji }),
                        })
                            .then((response) => response.ok ? response.json() : Promise.reject())
                            .then((data) => { this.counts = data.counts ?? {}; this.mine = data.mine ?? null; })
                            .catch(() => {});
                    },
                }"
                class="flex flex-wrap items-center gap-1.5"
            >
                @foreach (\App\Models\PostReaction::EMOJIS as $emoji)
                    <button
                        type="button"
                        @click="react('{{ $emoji }}')"
                        aria-label="React with {{ $emoji }}"
                        class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs transition"
                        :class="mine === '{{ $emoji }}' ? 'border-indigo-400 bg-indigo-500/10 text-indigo-500 dark:text-indigo-400' : 'border-zinc-200 text-zinc-500 hover:border-indigo-300 dark:border-white/10 dark:text-zinc-400'"
                    >
                        <span>{{ $emoji }}</span>
                        <span x-show="counts['{{ $emoji }}']" x-text="counts['{{ $emoji }}']"></span>
                    </button>
                @endforeach
            </div>

            <div x-data="{ copied: false }" class="flex flex-wrap items-center gap-3">
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode($postUrl) }}" target="_blank" rel="noopener" aria-label="Share on X" class="grid size-9 place-items-center rounded-full border border-zinc-300 text-zinc-600 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-300">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>

                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($postUrl) }}" target="_blank" rel="noopener" aria-label="Share on LinkedIn" class="grid size-9 place-items-center rounded-full border border-zinc-300 text-zinc-600 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-300">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 110-4.124 2.062 2.062 0 010 4.124zM7.114 20.452H3.56V9h3.554v11.452z"/></svg>
                </a>

                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($postUrl) }}" target="_blank" rel="noopener" aria-label="Share on Facebook" class="grid size-9 place-items-center rounded-full border border-zinc-300 text-zinc-600 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-300">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4" fill="currentColor"><path d="M22 12.06C22 6.505 17.523 2 12 2S2 6.505 2 12.06c0 5.02 3.657 9.184 8.438 9.94v-7.03H7.898v-2.91h2.54V9.845c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.459h-1.26c-1.243 0-1.63.771-1.63 1.562v1.875h2.773l-.443 2.91h-2.33V22c4.78-.756 8.437-4.92 8.437-9.94z"/></svg>
                </a>

                <button
                    type="button"
                    @click="navigator.clipboard.writeText(@js($postUrl)); copied = true; setTimeout(() => copied = false, 2000)"
                    aria-label="Copy link"
                    class="grid size-9 place-items-center rounded-full border border-zinc-300 text-zinc-600 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-300"
                >
                    <svg x-show="!copied" xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                    <svg x-show="copied" x-cloak xmlns="http://www.w3.org/2000/svg" class="size-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>
        </div>

        @if ($showToc)
            <nav aria-label="Table of contents" class="mt-8 rounded-2xl border border-zinc-200 bg-zinc-50 p-6 dark:border-white/10 dark:bg-white/[0.03]">
                <p class="text-sm font-semibold uppercase tracking-wide text-zinc-900 dark:text-white">In this article</p>
                <ol class="mt-3 list-inside list-decimal space-y-1.5 text-sm text-zinc-600 dark:text-zinc-400">
                    @foreach ($tocHeadings as $heading)
                        <li><a href="#{{ $heading['id'] }}" class="transition hover:text-indigo-500 dark:hover:text-indigo-400">{{ $heading['text'] }}</a></li>
                    @endforeach
                </ol>
            </nav>
        @endif

        <div class="prose dark:prose-invert prose-zinc mt-8 max-w-none prose-a:text-indigo-500 dark:prose-a:text-indigo-400">
            {!! $post->body_html !!}
        </div>

        @if ($post->tags->isNotEmpty())
            <div class="mt-10 flex flex-wrap gap-2 border-t border-zinc-200 pt-6 dark:border-white/10">
                @foreach ($post->tags as $tag)
                    <span class="rounded-full border border-zinc-300 px-3 py-1 text-xs font-medium text-zinc-600 dark:border-white/15 dark:text-zinc-400">#{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif

        <aside class="mt-10 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-indigo-500/30 bg-indigo-500/10 p-6">
            <p class="max-w-xl text-sm font-medium text-zinc-800 dark:text-zinc-100">Need this built? I do LLM integration, AI automation and full-stack development work.</p>
            <a href="{{ route('services.index') }}" class="rounded-full bg-zinc-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">See my services &rarr;</a>
        </aside>

        <section aria-label="About the author" class="mt-10 flex flex-col gap-5 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-zinc-900 sm:flex-row sm:items-center">
            <img src="{{ asset(config('seo.person.image')) }}" alt="{{ config('seo.person.name') }}" width="80" height="80" loading="lazy" decoding="async" class="size-20 shrink-0 rounded-full bg-indigo-500/10 object-cover object-top">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-500 dark:text-indigo-400">Written by</p>
                <p class="mt-1 text-lg font-bold text-zinc-900 dark:text-white">{{ config('seo.person.name') }}</p>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ config('seo.person.short_bio') }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm font-medium">
                    <a href="{{ route('about') }}" class="text-indigo-500 hover:underline dark:text-indigo-400">About me</a>
                    <a href="{{ config('seo.person.github') }}" target="_blank" rel="noopener me" class="text-zinc-600 hover:text-indigo-500 dark:text-zinc-300 dark:hover:text-indigo-400">GitHub</a>
                    <a href="{{ config('seo.person.linkedin') }}" target="_blank" rel="noopener me" class="text-zinc-600 hover:text-indigo-500 dark:text-zinc-300 dark:hover:text-indigo-400">LinkedIn</a>
                </div>
            </div>
        </section>

        @if ($related->isNotEmpty())
            <section aria-labelledby="related-posts" class="mt-14">
                <h2 id="related-posts" class="text-xl font-bold text-zinc-900 dark:text-white">Related posts</h2>
                <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @foreach ($related as $relatedPost)
                        <x-blog-card :post="$relatedPost" />
                    @endforeach
                </div>
            </section>
        @endif

        <div id="comments" class="mt-14 border-t border-zinc-200 pt-10 dark:border-white/10">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                {{ $totalComments }} {{ \Illuminate\Support\Str::plural('Comment', $totalComments) }}
            </h2>

            <div class="mt-8 space-y-6">
                @forelse ($post->comments as $comment)
                    <x-comment :comment="$comment" :post="$post" />
                @empty
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">No comments yet — be the first to share your thoughts.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('blog.comments.store', $post) }}#comments" class="mt-10 space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-zinc-900">
                @csrf

                <h3 class="font-semibold text-zinc-900 dark:text-white">Leave a comment</h3>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="comment-name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Name</label>
                        <input type="text" name="name" id="comment-name" value="{{ old('name') }}" required
                               class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-white/15 dark:text-white">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="comment-email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Email</label>
                        <input type="email" name="email" id="comment-email" value="{{ old('email') }}" required
                               class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-white/15 dark:text-white">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-zinc-400">Never published.</p>
                    </div>
                </div>

                <div>
                    <label for="comment-body" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Comment</label>
                    <textarea name="body" id="comment-body" rows="4" required
                              class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-white/15 dark:text-white">{{ old('body') }}</textarea>
                    @error('body') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="cf-turnstile" x-data x-init="window.renderTurnstileWhenReady($el)"></div>
                @error('cf-turnstile-response') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                <button type="submit" class="w-full rounded-full bg-zinc-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400 sm:w-auto">
                    Post Comment
                </button>
            </form>
        </div>
    </article>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js" defer></script>
    <script>document.addEventListener('DOMContentLoaded', () => window.hljs && hljs.highlightAll());</script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>
</x-layouts.app>
