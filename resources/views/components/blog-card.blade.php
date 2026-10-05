@props(['post', 'heading' => 'h3'])

@php
    $palettes = [
        'from-indigo-500 to-violet-500',
        'from-violet-500 to-fuchsia-500',
        'from-cyan-500 to-blue-500',
        'from-emerald-500 to-teal-500',
        'from-orange-500 to-rose-500',
        'from-blue-500 to-indigo-500',
    ];
    $palette = $palettes[crc32($post->title) % count($palettes)];
@endphp

<a href="{{ route('blog.show', $post) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:-translate-y-1 hover:border-indigo-400/50 hover:shadow-lg hover:shadow-indigo-500/5 dark:border-white/10 dark:bg-zinc-900">
    @if ($post->image_path)
        <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" width="{{ $post->image_width ?? 1600 }}" height="{{ $post->image_height ?? 900 }}" loading="lazy" decoding="async" class="h-40 w-full object-cover">
    @else
        <div class="relative flex h-40 items-center justify-center overflow-hidden bg-gradient-to-br {{ $palette }}">
            <div class="bg-grid absolute inset-0 opacity-30"></div>
            <span class="text-5xl font-bold text-white/25">{{ strtoupper(substr($post->title, 0, 1)) }}</span>
        </div>
    @endif

    <div class="flex flex-1 flex-col p-6">
        @if ($post->categories->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($post->categories as $category)
                    <span class="rounded-full bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-500 dark:text-indigo-300">{{ $category->name }}</span>
                @endforeach
            </div>
        @endif

        <{{ $heading }} class="mt-4 text-lg font-semibold text-zinc-900 transition group-hover:text-indigo-500 dark:text-white dark:group-hover:text-indigo-400">{{ $post->title }}</{{ $heading }}>

        @if ($post->excerpt)
            <p class="mt-2 line-clamp-2 flex-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $post->excerpt }}</p>
        @endif

        <div class="mt-4 flex items-center gap-3 text-xs text-zinc-400">
            <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->format('F j, Y') }}</time>
            <span class="inline-flex items-center gap-1">
                <x-icon name="eye" class="size-3.5" />
                {{ number_format($post->views) }}
            </span>
        </div>
    </div>
</a>
