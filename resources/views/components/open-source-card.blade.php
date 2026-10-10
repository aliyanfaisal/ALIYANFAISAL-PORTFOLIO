@props(['project'])

<a href="{{ route('open-source.show', $project['slug']) }}"
   class="group relative flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white p-6 transition hover:-translate-y-1 hover:border-indigo-400/50 hover:shadow-xl hover:shadow-indigo-500/10 dark:border-white/10 dark:bg-zinc-900">
    <div class="flex items-center justify-between gap-3">
        <span class="text-xs font-medium uppercase tracking-wide text-indigo-500 dark:text-indigo-400">{{ $project['category'] }}</span>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
            <span class="size-1.5 rounded-full bg-emerald-500"></span>{{ $project['status'] }}
        </span>
    </div>

    <h3 class="mt-3 text-xl font-semibold text-zinc-900 dark:text-white">{{ $project['name'] }}</h3>
    <p class="mt-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $project['tagline'] }}</p>
    <p class="mt-2 flex-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $project['summary'] }}</p>

    <div class="mt-4 flex flex-wrap gap-1.5">
        @foreach (array_slice($project['stack'], 0, 4) as $tech)
            <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs text-zinc-600 dark:bg-white/5 dark:text-zinc-300">{{ $tech }}</span>
        @endforeach
    </div>

    <span class="mt-5 inline-flex items-center gap-1 text-sm font-medium text-indigo-500 dark:text-indigo-400">
        View project
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </span>
</a>
