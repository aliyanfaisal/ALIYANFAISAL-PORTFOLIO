@php
    $projectsTitle = 'Projects & Case Studies — Aliyan Faisal, Full-Stack & AI Engineer';
    $projectsDescription = config('seo.descriptions.projects');
    $graph = [
        \App\Support\Seo\Schema::personLite(),
        \App\Support\Seo\Schema::collectionPage(route('projects.index'), $projectsTitle, $projectsDescription, route('projects.index').'#breadcrumb', \App\Support\Seo\Schema::projectList($projects)),
        \App\Support\Seo\Schema::breadcrumbs([['name' => 'Home', 'url' => route('home')], ['name' => 'Projects', 'url' => route('projects.index')]], route('projects.index')),
    ];
@endphp
<x-layouts.app :title="$projectsTitle" :description="$projectsDescription" :graph="$graph">
    <section class="mx-auto max-w-4xl px-6 py-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400">Portfolio</p>
        <h1 class="mt-3 text-4xl font-bold text-zinc-900 dark:text-white">Selected Work</h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
            A mix of client projects and open-source code &mdash; pulled live from
            <a href="https://github.com/{{ $githubProfile['login'] ?? 'aliyanfaisal' }}" target="_blank" rel="noopener" class="font-medium text-indigo-500 hover:underline dark:text-indigo-400">my GitHub</a>.
        </p>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-16">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Client Projects</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">A selection of my most recent work — not the full list of projects I've delivered.</p>
            </div>
            <a href="{{ route('projects.download') }}" class="inline-flex items-center gap-2 rounded-full border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 19h16"/></svg>
                Download project links (.txt)
            </a>
        </div>
        <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach ($projects as $project)
                <x-project-card :project="$project" />
            @endforeach
        </div>
    </section>

    <section class="border-t border-zinc-200 bg-zinc-50 py-16 dark:border-white/10 dark:bg-white/[0.02]">
        <div class="mx-auto max-w-6xl px-6">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">GitHub Repositories</h2>
                    @if ($githubProfile)
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $githubProfile['public_repos'] }} public repos &middot; {{ $githubProfile['followers'] }} followers
                        </p>
                    @endif
                </div>
                <a href="https://github.com/{{ $githubProfile['login'] ?? 'aliyanfaisal' }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-4" fill="currentColor"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.09 3.29 9.4 7.86 10.93.57.1.78-.25.78-.55 0-.27-.01-1.16-.02-2.11-3.2.7-3.87-1.36-3.87-1.36-.53-1.34-1.29-1.7-1.29-1.7-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.56-.29-5.26-1.28-5.26-5.7 0-1.26.45-2.29 1.19-3.09-.12-.29-.52-1.47.11-3.06 0 0 .97-.31 3.18 1.18a11 11 0 015.8 0c2.2-1.49 3.17-1.18 3.17-1.18.64 1.59.24 2.77.12 3.06.74.8 1.18 1.83 1.18 3.09 0 4.43-2.7 5.4-5.28 5.69.42.36.78 1.08.78 2.18 0 1.57-.02 2.84-.02 3.23 0 .3.2.66.79.55A10.52 10.52 0 0023.5 12c0-6.35-5.15-11.5-11.5-11.5Z"/></svg>
                    Follow on GitHub
                </a>
            </div>

            @if (empty($repos))
                <p class="mt-8 text-sm text-zinc-500 dark:text-zinc-400">Repositories couldn't be loaded right now &mdash; check back shortly.</p>
            @else
                <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($repos as $repo)
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
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
