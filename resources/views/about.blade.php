@php
    $aboutTitle = 'About Aliyan Faisal — Full-Stack Developer & AI / LLM Systems Engineer';
    $aboutDescription = config('seo.descriptions.about');
    $graph = [
        \App\Support\Seo\Schema::person(),
        \App\Support\Seo\Schema::website(),
        \App\Support\Seo\Schema::profilePage(route('about'), $aboutTitle, $aboutDescription),
        \App\Support\Seo\Schema::breadcrumbs([['name' => 'Home', 'url' => route('home')], ['name' => 'About', 'url' => route('about')]], route('about')),
    ];
@endphp
<x-layouts.app :title="$aboutTitle" :description="$aboutDescription" og-type="profile" :graph="$graph">
    <section class="mx-auto grid max-w-5xl items-center gap-10 px-6 py-16 md:grid-cols-[1fr_20rem]">
        <div class="text-center md:text-left">
            <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400">About Me</p>
            <h1 class="mt-3 text-4xl font-bold text-zinc-900 dark:text-white">Hi, I'm Aliyan Faisal.</h1>
            <p class="mt-6 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                A software engineer and AI developer — I integrate LLMs, RAG and automation into real products,
                and build and deploy the web apps (Laravel, Node.js, React and WordPress) and servers behind them.
                I have a BSc in Information Technology and have spent 5+ years building production systems for international clients and for products of my own.
            </p>
        </div>

        <div class="relative mx-auto w-64 md:w-full">
            <div class="absolute -inset-4 -z-10 rounded-full bg-gradient-to-br from-indigo-500/20 via-violet-500/15 to-cyan-400/20 blur-2xl"></div>
            <img src="{{ asset('images/aliyan_whiteshirt_redtie_cutout.png') }}" alt="Aliyan Faisal, software engineer and AI developer" width="699" height="764" fetchpriority="high" decoding="async" class="fade-top relative z-10 h-auto w-full rounded-b-[50%] drop-shadow-[0_20px_40px_rgba(79,70,229,0.35)]">
        </div>
    </section>

    {{-- Skills --}}
    <section class="border-t border-zinc-200 bg-zinc-50 py-16 dark:border-white/10 dark:bg-white/[0.02]">
        <div class="mx-auto max-w-5xl px-6">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Skills &amp; Expertise</h2>
            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                @foreach ($skills as $skill)
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $skill->name }}</span>
                            <span class="text-zinc-400">{{ $skill->proficiency }}%</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-white/10">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-cyan-400" style="width: {{ $skill->proficiency }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Experience --}}
    <section class="py-16">
        <div class="mx-auto max-w-4xl px-6">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Experience</h2>
            <div class="mt-8 space-y-8 border-l border-zinc-200 pl-8 dark:border-white/10">
                @foreach ($experiences as $experience)
                    <div class="relative">
                        <span class="absolute -left-[calc(2rem+5px)] top-1.5 size-2.5 rounded-full bg-indigo-500"></span>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $experience->title }}</h3>
                            @if ($experience->current)
                                <span class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">Current</span>
                            @endif
                        </div>
                        <p class="text-sm font-medium text-indigo-500 dark:text-indigo-400">{{ $experience->company }}</p>
                        <p class="mt-1 text-xs text-zinc-400">
                            {{ $experience->start_date?->format('Y') }} &mdash; {{ $experience->current ? 'Present' : $experience->end_date?->format('Y') }}
                            @if ($experience->employment_type)
                                &middot; {{ $experience->employment_type }}
                            @endif
                        </p>
                        <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $experience->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Education & Certifications --}}
    <section class="border-t border-zinc-200 bg-zinc-50 py-16 dark:border-white/10 dark:bg-white/[0.02]">
        <div class="mx-auto grid max-w-5xl gap-12 px-6 md:grid-cols-2">
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Education</h2>
                <div class="mt-6 space-y-4">
                    @foreach ($education as $item)
                        <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-white/10 dark:bg-zinc-900">
                            <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $item->degree }}</h3>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $item->school }}, {{ $item->country }}</p>
                            <p class="mt-1 text-xs text-zinc-400">{{ $item->from_year ? $item->from_year.' – ' : '' }}{{ $item->to_year }}</p>
                            @if ($item->cgpa)
                                <p class="mt-1 text-sm font-medium text-indigo-500 dark:text-indigo-400">CGPA: {{ $item->cgpa }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Certifications</h2>
                <div class="mt-6 space-y-3">
                    @foreach ($certifications as $certification)
                        <div class="flex items-center justify-between rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm dark:border-white/10 dark:bg-zinc-900">
                            <div>
                                <p class="font-medium text-zinc-800 dark:text-zinc-100">{{ $certification->name }}</p>
                                <p class="text-xs text-zinc-400">{{ $certification->issuer }}</p>
                            </div>
                            <span class="text-xs text-zinc-400">{{ $certification->year }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="mx-auto max-w-3xl px-6 text-center">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Want to talk about my work?</h2>
            <a href="{{ route('contact.create') }}" class="mt-6 inline-block rounded-full bg-zinc-900 px-8 py-3 text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                Get in Touch
            </a>
        </div>
    </section>
</x-layouts.app>
