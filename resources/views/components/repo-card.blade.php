@props(['repo'])
<a href="{{ $repo['url'] }}" target="_blank" rel="noopener" class="group flex flex-col rounded-2xl border border-zinc-200 bg-white p-6 transition hover:-translate-y-1 hover:border-indigo-400/50 hover:shadow-lg hover:shadow-indigo-500/5 dark:border-white/10 dark:bg-zinc-900">
    <div class="flex items-center justify-between">
        <h3 class="truncate font-semibold text-zinc-900 dark:text-white">{{ $repo['name'] }}</h3>
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-zinc-400 transition group-hover:text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg>
    </div>
    @if ($repo['pinned'] ?? false)
        <span class="mt-1 inline-flex w-fit items-center gap-1 rounded-full bg-indigo-500/10 px-2 py-0.5 text-[10px] font-medium text-indigo-500 dark:text-indigo-400">
            <x-icon name="bookmark" class="size-3" /> Pinned
        </span>
    @endif
    <p class="mt-2 line-clamp-2 flex-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $repo['description'] ?? 'No description provided.' }}</p>
    <div class="mt-4 flex items-center gap-4 text-xs text-zinc-400">
        @if ($repo['language'])
            <span class="inline-flex items-center gap-1.5">
                <span class="size-2 rounded-full bg-indigo-400"></span>
                {{ $repo['language'] }}
            </span>
        @endif
        <span class="inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-3.5"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118l-3.37-2.448c-.782-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.285-3.958z"/></svg>
            {{ $repo['stars'] }}
        </span>
    </div>
</a>
