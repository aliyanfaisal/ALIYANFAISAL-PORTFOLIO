@php
    $url = route('open-source.show', $project['slug']);
    $graph = [
        \App\Support\Seo\Schema::personLite(),
        array_filter([
            '@type' => 'SoftwareSourceCode',
            '@id' => $url.'#project',
            'name' => $project['name'],
            'url' => $url,
            'description' => $project['description'],
            'codeRepository' => $project['repo_url'],
            'programmingLanguage' => $project['language'] ?? null,
            'license' => isset($project['license']) && $project['license'] === 'MIT' ? 'https://opensource.org/licenses/MIT' : null,
            'version' => $project['version'] ?? null,
            'author' => \App\Support\Seo\Schema::personRef(),
        ]),
        \App\Support\Seo\Schema::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Open Source', 'url' => route('open-source.index')],
            ['name' => $project['name'], 'url' => $url],
        ], $url),
    ];
@endphp
<x-layouts.app :title="$project['name'].' — '.$project['tagline'].' | Aliyan Faisal'" :description="\Illuminate\Support\Str::limit($project['description'], 157)" :graph="$graph" :full-bleed="true">
    {{-- Hero --}}
    <section class="glow-gradient relative overflow-hidden border-b border-zinc-200 pt-20 dark:border-white/10">
        <div class="bg-grid absolute inset-0 -z-10"></div>
        <div class="mx-auto max-w-4xl px-6 pb-14 pt-12">
            <nav aria-label="Breadcrumb" class="text-sm text-zinc-500 dark:text-zinc-400">
                <a href="{{ route('open-source.index') }}" class="hover:text-indigo-500">Open Source</a>
                <span class="mx-1.5">/</span><span class="text-zinc-700 dark:text-zinc-200">{{ $project['name'] }}</span>
            </nav>

            <div class="mt-6 flex flex-wrap items-center gap-2 text-xs font-medium">
                <span class="rounded-full bg-indigo-500/10 px-3 py-1 uppercase tracking-wide text-indigo-500 dark:text-indigo-300">{{ $project['category'] }}</span>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1 text-emerald-600 dark:text-emerald-400"><span class="size-1.5 rounded-full bg-emerald-500"></span>{{ $project['status'] }}</span>
                @isset($project['version'])<span class="rounded-full border border-zinc-300 px-3 py-1 text-zinc-600 dark:border-white/15 dark:text-zinc-300">v{{ $project['version'] }}</span>@endisset
            </div>

            <h1 class="mt-4 text-4xl font-bold tracking-tight text-zinc-900 sm:text-5xl dark:text-white">{{ $project['name'] }}</h1>
            <p class="mt-3 text-xl font-medium text-zinc-700 dark:text-zinc-200">{{ $project['tagline'] }}</p>
            <p class="mt-4 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">{{ $project['summary'] }}</p>

            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ $project['repo_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-zinc-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                    <x-brand-icon name="github" class="size-4" /> View on GitHub
                </a>
                @isset($project['install'])
                    <a href="#install" class="rounded-full border border-zinc-300 px-6 py-3 text-sm font-semibold text-zinc-700 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-200">Install</a>
                @endisset
            </div>

            <dl class="mt-10 grid grid-cols-2 gap-6 border-t border-zinc-200 pt-6 text-sm sm:grid-cols-4 dark:border-white/10">
                @foreach ([['License', $project['license'] ?? null], ['Requires', $project['requires'] ?? null], ['Language', $project['language'] ?? null], ['Network', 'None. Runs locally']] as [$label, $value])
                    @if ($value)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-400">{{ $label }}</dt>
                            <dd class="mt-1 font-semibold text-zinc-900 dark:text-white">{{ $value }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>
        </div>
    </section>

    <div class="mx-auto max-w-4xl space-y-16 px-6 py-14">
        {{-- Highlights --}}
        @isset($project['highlights'])
            <section>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">What it does</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    @foreach ($project['highlights'] as [$heading, $text])
                        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-900">
                            <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $heading }}</h3>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $text }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endisset

        {{-- Walkthrough (optional per-project partial) --}}
        @includeIf('open-source.previews.'.$project['slug'])

        {{-- Install & try --}}
        @isset($project['install'])
            <section id="install" class="scroll-mt-24">
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Install</h2>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Two commands in a terminal, then restart your Claude Code session.</p>
                <div class="mt-5 space-y-3">
                    @foreach ($project['install'] as $command)
                        <div x-data="{ copied: false }" class="flex items-center justify-between gap-3 rounded-xl bg-zinc-950 px-4 py-3 font-mono text-sm text-zinc-100">
                            <code class="min-w-0 break-all"><span class="select-none text-zinc-500">$ </span>{{ $command }}</code>
                            <button type="button" @click="navigator.clipboard.writeText(@js($command)).then(() => { copied = true; setTimeout(() => copied = false, 1500) })" class="shrink-0 rounded-md border border-white/15 px-2.5 py-1 text-xs text-zinc-300 transition hover:bg-white/10" x-text="copied ? 'Copied' : 'Copy'">Copy</button>
                        </div>
                    @endforeach
                </div>
                @isset($project['try'])
                    <p class="mt-5 text-sm text-zinc-500 dark:text-zinc-400">Then try:</p>
                    <div class="mt-2 rounded-xl bg-zinc-950 px-4 py-3 font-mono text-sm text-indigo-300"><code>{{ $project['try'] }}</code></div>
                @endisset
            </section>
        @endisset

        {{-- Commands --}}
        @isset($project['commands'])
            <section>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Commands</h2>
                <div class="mt-5 divide-y divide-zinc-200 overflow-hidden rounded-2xl border border-zinc-200 bg-white dark:divide-white/10 dark:border-white/10 dark:bg-zinc-900">
                    @foreach ($project['commands'] as [$command, $text, $userOnly])
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 px-5 py-4">
                            <code class="font-mono text-sm font-semibold text-indigo-600 dark:text-indigo-300">{{ $command }}</code>
                            <span class="flex-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $text }}</span>
                            @if ($userOnly)
                                <span class="rounded-full bg-amber-500/10 px-2.5 py-0.5 text-xs font-medium text-amber-600 dark:text-amber-400">You only</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                @isset($project['commands_note'])
                    <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">{{ $project['commands_note'] }}</p>
                @endisset
            </section>
        @endisset

        {{-- How it works --}}
        @isset($project['how_it_works'])
            <section>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">How it works</h2>
                <ol class="mt-6 space-y-5 border-l border-zinc-200 pl-6 dark:border-white/10">
                    @foreach ($project['how_it_works'] as [$heading, $text])
                        <li class="relative">
                            <span class="absolute -left-[calc(1.5rem+5px)] top-1.5 size-2.5 rounded-full bg-indigo-500"></span>
                            <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $heading }}</h3>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $text }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endisset

        {{-- Architecture --}}
        @isset($project['architecture'])
            <section>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">What's in the repository</h2>
                <dl class="mt-5 grid gap-3">
                    @foreach ($project['architecture'] as [$path, $text])
                        <div class="rounded-xl border border-zinc-200 bg-white px-5 py-3 sm:flex sm:items-baseline sm:gap-4 dark:border-white/10 dark:bg-zinc-900">
                            <dt class="shrink-0 font-mono text-sm font-semibold text-zinc-900 sm:w-44 dark:text-white">{{ $path }}</dt>
                            <dd class="mt-1 text-sm text-zinc-500 sm:mt-0 dark:text-zinc-400">{{ $text }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endisset

        {{-- Data model --}}
        @isset($project['data_model'])
            <section>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Data model</h2>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Each note is one row in SQLite with these fields.</p>
                <dl class="mt-5 grid gap-3">
                    @foreach ($project['data_model'] as [$group, $text])
                        <div class="rounded-xl border border-zinc-200 bg-white px-5 py-3 sm:flex sm:items-baseline sm:gap-4 dark:border-white/10 dark:bg-zinc-900">
                            <dt class="shrink-0 text-sm font-semibold text-zinc-900 sm:w-44 dark:text-white">{{ $group }}</dt>
                            <dd class="mt-1 font-mono text-xs text-zinc-500 sm:mt-0 dark:text-zinc-400">{{ $text }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endisset

        {{-- Roadmap --}}
        @isset($project['roadmap'])
            <section>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Where it's headed</h2>
                <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">The schema and CLI already carry fields that the commands don't fully use yet. This is the direction they point to.</p>
                <ul class="mt-5 space-y-2">
                    @foreach ($project['roadmap'] as $item)
                        <li class="flex gap-3 text-sm text-zinc-600 dark:text-zinc-300">
                            <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-indigo-400"></span>{{ $item }}
                        </li>
                    @endforeach
                </ul>
            </section>
        @endisset

        {{-- Stack --}}
        <section>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Built with</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($project['stack'] as $tech)
                    <span class="rounded-full bg-zinc-100 px-3 py-1.5 text-sm text-zinc-700 dark:bg-white/5 dark:text-zinc-300">{{ $tech }}</span>
                @endforeach
            </div>
        </section>

        {{-- CTA / others --}}
        <section class="rounded-3xl border border-indigo-500/20 bg-indigo-500/5 p-8 text-center">
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Use it, read it, improve it</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Issues and pull requests are welcome on GitHub.</p>
            <a href="{{ $project['repo_url'] }}" target="_blank" rel="noopener" class="mt-5 inline-flex items-center gap-2 rounded-full bg-zinc-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                <x-brand-icon name="github" class="size-4" /> {{ parse_url($project['repo_url'], PHP_URL_PATH) ? ltrim(parse_url($project['repo_url'], PHP_URL_PATH), '/') : $project['repo_url'] }}
            </a>
        </section>

        @if (count($others))
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Also open source:
                @foreach ($others as $other)
                    <a href="{{ route('open-source.show', $other['slug']) }}" class="font-semibold text-indigo-500 hover:underline dark:text-indigo-400">{{ $other['name'] }}</a> &mdash; {{ $other['tagline'] }}@unless ($loop->last), @endunless
                @endforeach
            </p>
        @endif
    </div>
</x-layouts.app>
