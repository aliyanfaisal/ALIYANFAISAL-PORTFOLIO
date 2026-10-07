@php
    $graph = [
        \App\Support\Seo\Schema::personLite(),
        [
            '@type' => 'SoftwareApplication',
            '@id' => route('products.show', $product['slug']).'#product',
            'name' => $product['name'],
            'url' => $product['url'],
            'description' => $product['description'],
            'applicationCategory' => 'DeveloperApplication',
            'operatingSystem' => 'Web',
            'author' => \App\Support\Seo\Schema::personRef(),
        ],
        \App\Support\Seo\Schema::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Products', 'url' => route('products.index')],
            ['name' => $product['name'], 'url' => route('products.show', $product['slug'])],
        ], route('products.show', $product['slug'])),
    ];

    $lanes = [
        ['Fix', 'My prompt isn’t working', 'Debug, optimize, format and score a prompt that gives weak answers.', 'emerald', 'M9 12.75L11.25 15 15 9.75M21 12c0 5-4 9-9 9s-9-4-9-9 4-9 9-9 9 4 9 9z'],
        ['Build', 'I need a prompt written', 'Describe the goal in plain words and get a structured, ready-to-use prompt.', 'orange', 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z'],
        ['Find', 'Find a ready-made prompt', 'Browse the prompt book: tested prompts with examples and an editor to make them yours.', 'violet', 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
    ];
    $laneColors = [
        'emerald' => ['bg-emerald-50 border-emerald-200', 'bg-emerald-100 text-emerald-600', 'text-emerald-700'],
        'orange' => ['bg-orange-50 border-orange-200', 'bg-orange-100 text-orange-600', 'text-orange-700'],
        'violet' => ['bg-violet-50 border-violet-200', 'bg-violet-100 text-violet-600', 'text-violet-700'],
    ];

    $tools = [
        ['Prompt Builder', 'Turn a rough idea into a structured prompt.'],
        ['Prompt Optimizer', 'Sharpen a prompt for clearer, better answers.'],
        ['Prompt Debugger', 'Find out why a prompt keeps failing.'],
        ['Prompt Formatter', 'Clean up a messy prompt into a tidy one.'],
        ['Intelligence Score', 'Score a prompt and see how to improve it.'],
        ['Token Optimizer', 'Compress a prompt and cut token use.'],
        ['Document to Prompt', 'Upload a PDF or DOCX and pull out the relevant parts with retrieval.'],
        ['Site to Prompt', 'Turn a website’s design or content into a prompt.'],
        ['Compare & Estimate', 'Compare two prompt versions and estimate cost.'],
        ['Context Extractor', 'Extract the context an AI needs from your material.'],
    ];
    $toolColors = ['bg-violet-100 text-violet-600', 'bg-indigo-100 text-indigo-600', 'bg-emerald-100 text-emerald-600', 'bg-sky-100 text-sky-600', 'bg-pink-100 text-pink-600'];

    $flow = [
        ['Ask', 'Type your problem in your own words, or tap one of the one-click situations.'],
        ['Match', 'Text embeddings match what you wrote to the right tool and to relevant prompts.'],
        ['Result', 'The tool runs on the same page. No tab-hopping, no setup.'],
    ];
@endphp
<x-layouts.app :title="'Cuelara — AI Toolkit & Prompt Book | Aliyan Faisal'" :description="\Illuminate\Support\Str::limit($product['description'], 157)" :graph="$graph">
    <div class="relative overflow-hidden bg-[#faf9ff] text-zinc-600" style="background-image: linear-gradient(rgba(124,58,237,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(124,58,237,.05) 1px, transparent 1px); background-size: 40px 40px">
        {{-- floating shapes --}}
        <div class="pointer-events-none absolute inset-0 hidden sm:block" aria-hidden="true">
            <span class="animate-float absolute left-[6%] top-[14%] grid size-16 rotate-[-12deg] place-items-center rounded-3xl bg-violet-200/70 text-violet-500 shadow-lg shadow-violet-300/30"><x-icon name="sparkles" class="size-7" /></span>
            <span class="animate-float absolute right-[7%] top-[12%] grid size-20 place-items-center rounded-full bg-violet-200/60 text-violet-500 shadow-lg shadow-violet-300/30" style="animation-delay: 1.2s"><x-icon name="code" class="size-8" /></span>
            <span class="animate-float absolute right-[5%] top-[40%] grid size-14 place-items-center rounded-full bg-amber-100 text-amber-500 shadow-lg shadow-amber-300/30" style="animation-delay: 2.4s"><x-icon name="bolt" class="size-6" /></span>
            <span class="animate-float absolute left-[4%] top-[46%] grid size-14 rotate-12 place-items-center rounded-2xl bg-orange-100 text-orange-500 shadow-lg shadow-orange-300/30" style="animation-delay: 3.1s"><x-icon name="wrench" class="size-6" /></span>
        </div>

        {{-- Hero --}}
        <section class="relative mx-auto max-w-6xl px-6 pb-20 pt-12 text-center">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-zinc-500 hover:text-violet-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                All products
            </a>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                @foreach ($lanes as [$lane, , , $color])
                    <span class="rounded-full border px-4 py-1.5 text-sm font-semibold text-zinc-800 {{ $laneColors[$color][0] }}">{{ $lane }}</span>
                @endforeach
            </div>

            <h1 class="mx-auto mt-6 max-w-3xl text-5xl font-extrabold tracking-tight text-zinc-900 sm:text-7xl">
                Find, fix or build
                <span class="relative inline-block bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">the perfect prompt.
                    <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 300 12" preserveAspectRatio="none" aria-hidden="true"><path d="M2 8 Q 75 2 150 6 T 298 5" fill="none" stroke="#a78bfa" stroke-width="3" stroke-linecap="round"/></svg>
                </span>
            </h1>
            <p class="mx-auto mt-7 max-w-2xl text-lg text-zinc-500">Smart tools and ready-made prompts that help you get better answers from any AI. Built for people who find prompts hard.</p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ $product['url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:-translate-y-0.5 hover:bg-indigo-700">
                    Try Cuelara free
                    <x-icon name="arrow-up-right" class="size-4" />
                </a>
                <a href="#tools" class="rounded-full border border-violet-200 bg-white px-7 py-3.5 text-sm font-semibold text-zinc-700 shadow-sm transition hover:border-violet-400">Explore the tools</a>
            </div>

            {{-- screenshot in soft card --}}
            <div class="relative mx-auto mt-16 max-w-5xl">
                <div class="absolute -inset-3 rounded-[2rem] bg-gradient-to-br from-violet-300/50 via-indigo-200/40 to-pink-200/50 blur-xl"></div>
                <div class="relative overflow-hidden rounded-[1.75rem] border border-violet-200 bg-white p-2 shadow-2xl shadow-violet-500/20">
                    <img src="{{ asset($product['image']) }}" alt="Cuelara homepage: ask what you need help with and find the right tool" width="1920" height="1089" class="w-full rounded-[1.25rem]">
                </div>
            </div>
        </section>

        {{-- Fix / Build / Find --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-zinc-900">Three ways in. One place.</h2>
                <p class="mx-auto mt-3 max-w-xl text-zinc-500">Whatever you’re stuck on, there’s a lane for it.</p>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($lanes as [$lane, $headline, $text, $color, $path])
                    <div class="rounded-3xl border p-7 transition hover:-translate-y-1 hover:shadow-xl {{ $laneColors[$color][0] }}">
                        <span class="grid size-12 place-items-center rounded-2xl {{ $laneColors[$color][1] }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                        </span>
                        <p class="mt-5 text-sm font-bold uppercase tracking-wide {{ $laneColors[$color][2] }}">{{ $lane }}</p>
                        <h3 class="mt-1 text-xl font-bold text-zinc-900">{{ $headline }}</h3>
                        <p class="mt-2 text-sm text-zinc-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Ask → Match → Result --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="rounded-[2rem] border border-violet-200 bg-white p-8 shadow-xl shadow-violet-500/10 sm:p-12">
                <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-violet-600">The guided flow</span>
                        <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-zinc-900">Say what you need. Cuelara picks the tool.</h2>
                        <p class="mt-4 text-zinc-500">No menus to learn and no prompt-engineering jargon. Describe the problem in your own words and the site does the matching.</p>
                        <ol class="mt-8 space-y-5">
                            @foreach ($flow as $i => [$name, $text])
                                <li class="flex gap-4">
                                    <span class="grid size-9 shrink-0 place-items-center rounded-full bg-violet-100 text-sm font-bold text-violet-700">{{ $i + 1 }}</span>
                                    <div><h3 class="font-bold text-zinc-900">{{ $name }}</h3><p class="text-sm text-zinc-500">{{ $text }}</p></div>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    {{-- mock --}}
                    <div class="rounded-2xl border border-violet-100 bg-[#faf9ff] p-5">
                        <p class="text-sm font-bold text-zinc-900">What do you need help with?</p>
                        <div class="mt-3 flex items-center gap-2 rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm text-zinc-400">
                            <x-icon name="eye" class="size-4" />Write a cold email to a SaaS founder
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2 text-xs font-medium text-zinc-600">
                            @foreach (['My prompt isn’t working', 'I need a prompt written', 'Make my prompt shorter', 'Score my prompt', 'Pull answers from a long PDF'] as $chip)
                                <span class="rounded-full border border-zinc-200 bg-white px-3 py-1.5">{{ $chip }}</span>
                            @endforeach
                        </div>
                        <div class="mt-5 flex items-center gap-3 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white">
                            <x-icon name="sparkles" class="size-4" />Matched: Prompt Builder
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Tools --}}
        <section id="tools" class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-violet-600">The toolkit</span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-zinc-900">Ten tools that run on the page</h2>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($tools as $i => [$name, $text])
                    <div class="rounded-2xl border border-zinc-200 bg-white p-5 transition hover:-translate-y-1 hover:border-violet-300 hover:shadow-lg hover:shadow-violet-500/10">
                        <span class="grid size-10 place-items-center rounded-xl text-sm font-extrabold {{ $toolColors[$i % count($toolColors)] }}">{{ $i + 1 }}</span>
                        <h3 class="mt-4 text-sm font-bold text-zinc-900">{{ $name }}</h3>
                        <p class="mt-1 text-xs leading-relaxed text-zinc-500">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Prompt book + extension --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-3xl bg-gradient-to-br from-violet-600 to-indigo-700 p-8 text-white shadow-xl shadow-violet-500/25">
                    <x-icon name="bookmark" class="size-8 text-violet-200" />
                    <h3 class="mt-5 text-2xl font-extrabold">The prompt book</h3>
                    <p class="mt-3 text-violet-100">Ready-made prompts with examples, search, and an “Edit this prompt” editor so you can adapt any of them in seconds.</p>
                </div>
                <div class="rounded-3xl border border-zinc-200 bg-white p-8 shadow-sm">
                    <x-icon name="cube" class="size-8 text-violet-500" />
                    <h3 class="mt-5 text-2xl font-extrabold text-zinc-900">Browser extension</h3>
                    <p class="mt-3 text-zinc-500">A popup hub and an in-field tooltip that bring the tools to the sites you already use, with a per-site block list and login through a personal access token.</p>
                </div>
            </div>
        </section>

        {{-- Developers --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-violet-600">For developers</span>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-zinc-900">An MCP server and a public API</h2>
                    <p class="mt-4 text-zinc-500">Use Cuelara from Claude, Cursor, Copilot and more through the MCP server, or call the public API directly to build, optimize, debug, format and compress prompts. API keys and docs included.</p>
                    <div class="mt-6 flex flex-wrap gap-2 text-xs font-semibold">
                        @foreach (['/api/mcp', '/api/v1/build', '/api/v1/optimize', '/api/v1/debug', '/api/v1/format', '/api/v1/compress'] as $endpoint)
                            <span class="rounded-full bg-violet-100 px-3 py-1.5 font-mono text-violet-700">{{ $endpoint }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="overflow-hidden rounded-2xl bg-zinc-900 shadow-xl">
                    <div class="flex items-center gap-2 border-b border-white/10 px-4 py-3"><span class="size-2.5 rounded-full bg-rose-400/70"></span><span class="size-2.5 rounded-full bg-amber-400/70"></span><span class="size-2.5 rounded-full bg-emerald-400/70"></span><span class="ml-3 text-xs text-zinc-500">optimize.sh</span></div>
<pre class="overflow-x-auto p-5 text-sm leading-relaxed text-zinc-300"><code><span class="text-zinc-500"># Sharpen a prompt through the API</span>
curl https://cuelara.com/api/v1/optimize \
  -H "Authorization: Bearer $CUELARA_API_KEY" \
  -d '{"prompt": "write me a blog post"}'</code></pre>
                </div>
            </div>
        </section>

        {{-- Built with --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="rounded-3xl border border-zinc-200 bg-white p-8 sm:p-10">
                <h2 class="text-2xl font-extrabold text-zinc-900">Built with</h2>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($product['stack'] as $tech)
                        <span class="rounded-full bg-violet-50 px-4 py-2 text-sm font-medium text-violet-700">{{ $tech }}</span>
                    @endforeach
                </div>
                <p class="mt-5 max-w-3xl text-sm text-zinc-500">Accounts, workspaces, per-tool plan limits and billing sit behind it, with a model-chain fallback across several LLM providers, rate limiting and an admin area to run it all.</p>
            </div>
        </section>

        {{-- CTA --}}
        <section class="relative px-6 pb-24 pt-8">
            <div class="mx-auto max-w-4xl rounded-[2rem] bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-600 p-10 text-center shadow-2xl shadow-violet-500/30 sm:p-14">
                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Stuck on a prompt? Start here.</h2>
                <p class="mx-auto mt-3 max-w-lg text-violet-100">Tell Cuelara what you need and let it find the tool.</p>
                <a href="{{ $product['url'] }}" target="_blank" rel="noopener" class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 text-sm font-bold text-indigo-700 transition hover:-translate-y-0.5 hover:bg-violet-50">
                    Open Cuelara
                    <x-icon name="arrow-up-right" class="size-4" />
                </a>
            </div>

            @foreach ($others as $other)
                <p class="mt-10 text-center text-sm text-zinc-500">
                    Also by me: <a href="{{ route('products.show', $other['slug']) }}" class="font-semibold text-violet-600 hover:underline">{{ $other['name'] }}</a> &mdash; {{ $other['tagline'] }}
                </p>
            @endforeach
        </section>
    </div>
</x-layouts.app>
