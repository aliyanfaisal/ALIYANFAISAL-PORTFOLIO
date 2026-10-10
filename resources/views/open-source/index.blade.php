@php
    $title = 'Open Source — Aliyan Faisal';
    $description = 'Open-source projects built by Aliyan Faisal, including Notemind, a Claude Code plugin for plain-words notes and reminders.';
    $graph = [
        \App\Support\Seo\Schema::personLite(),
        \App\Support\Seo\Schema::collectionPage(route('open-source.index'), 'Open Source', $description, route('open-source.index').'#breadcrumb', \App\Support\Seo\Schema::itemList(collect($projects)->map(fn ($p) => ['url' => route('open-source.show', $p['slug']), 'name' => $p['name']])->values()->all())),
        \App\Support\Seo\Schema::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Open Source', 'url' => route('open-source.index')],
        ], route('open-source.index')),
    ];
@endphp
<x-layouts.app :title="$title" :description="$description" :graph="$graph">
    <section class="mx-auto max-w-6xl px-6 py-12">
        <span class="text-xs font-medium uppercase tracking-wide text-indigo-500 dark:text-indigo-400">Free to use, free to read</span>
        <h1 class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">Open Source I Build</h1>
        <p class="mt-3 max-w-2xl text-zinc-500 dark:text-zinc-400">Small tools I build for my own workflow and share publicly. Each one has its own repository, so you can read the code, use it or contribute.</p>

        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($projects as $project)
                <x-open-source-card :project="$project" />
            @endforeach
        </div>

        <p class="mt-10 text-sm text-zinc-500 dark:text-zinc-400">
            More of my code is on <a href="https://github.com/aliyanfaisal" target="_blank" rel="noopener" class="font-medium text-indigo-500 hover:underline dark:text-indigo-400">GitHub</a>
            and the <a href="{{ route('projects.index') }}" class="font-medium text-indigo-500 hover:underline dark:text-indigo-400">Projects page</a>.
        </p>
    </section>
</x-layouts.app>
