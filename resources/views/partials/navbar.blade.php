@php
    $navLinks = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Products', 'route' => 'products.index'],
        ['label' => 'Open Source', 'route' => 'open-source.index'],
        ['label' => 'Blog', 'route' => 'blog.index'],
        ['label' => 'Contact', 'route' => 'contact.create'],
    ];
    $settings = \App\Models\Setting::current();
@endphp

<header
    x-data="{ mobileOpen: false, scrolled: false }"
    x-init="
        scrolled = window.scrollY > 24;
        window.addEventListener('scroll', () => { scrolled = window.scrollY > 24 }, { passive: true });
    "
    x-effect="document.documentElement.classList.toggle('overflow-hidden', mobileOpen)"
    @keydown.escape.window="mobileOpen = false"
    class="fixed inset-x-0 top-0 z-50 px-4 py-4"
>
    <div class="mx-auto flex max-w-6xl items-center justify-center">
        <nav
            class="flex w-full items-center gap-1 rounded-full border border-zinc-200/80 bg-white/90 px-2 py-2 shadow-lg shadow-zinc-900/10 backdrop-blur-xl transition-all duration-300 md:w-auto md:max-w-fit dark:border-white/10 dark:bg-zinc-900/90 dark:shadow-black/50 dark:ring-1 dark:ring-white/5"
            :class="scrolled ? 'shadow-xl dark:shadow-black/70' : ''"
        >
            <button @click="mobileOpen = !mobileOpen" type="button" class="grid size-9 shrink-0 place-items-center rounded-full text-zinc-600 transition hover:bg-zinc-900/5 md:hidden dark:text-zinc-300 dark:hover:bg-white/10" aria-label="Toggle menu">
                <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>

            <div class="hidden items-center gap-1 md:flex">
                @foreach ($navLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="relative rounded-full px-4 py-2 text-sm font-medium transition {{ request()->routeIs($link['route']) ? 'text-indigo-500 dark:text-indigo-400' : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white' }}">
                        @if (request()->routeIs($link['route']))
                            <span class="absolute inset-0 -z-10 rounded-full bg-gradient-to-r from-indigo-500/15 via-violet-500/15 to-cyan-400/15 dark:from-indigo-500/15 dark:via-violet-500/15 dark:to-cyan-400/10"></span>
                        @endif
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <a href="{{ route('home') }}" class="flex-1 truncate px-1 text-center text-sm font-semibold tracking-tight text-zinc-900 md:hidden dark:text-white">
                {{ strtoupper($settings->site_name) }}<span class="text-gradient">.</span>
            </a>

            <div class="mx-1 hidden h-6 w-px bg-zinc-200 md:block dark:bg-white/10"></div>

            <button
                @click="$store.theme.toggle()"
                type="button"
                aria-label="Toggle dark mode"
                class="grid size-9 shrink-0 place-items-center rounded-full text-zinc-500 transition hover:bg-zinc-900/5 hover:text-indigo-500 dark:text-zinc-400 dark:hover:bg-white/10 dark:hover:text-indigo-400"
            >
                <svg x-show="$store.theme.value === 'dark'" xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                <svg x-show="$store.theme.value !== 'dark'" xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
            </button>

            <a href="{{ route('contact.create') }}" class="ml-1 hidden shrink-0 rounded-full bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-600 md:inline-block dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                Contact
            </a>
        </nav>
    </div>

    <div
        x-show="mobileOpen"
        x-cloak
        class="fixed inset-0 z-40 md:hidden"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm" @click="mobileOpen = false"></div>

        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-full flex-col overflow-y-auto bg-white px-8 pt-28 pb-10 shadow-2xl dark:bg-zinc-950"
        >
            <div class="bg-grid pointer-events-none absolute inset-0"></div>

            <nav class="relative flex flex-1 flex-col justify-center gap-1">
                @foreach ($navLinks as $i => $link)
                    <a href="{{ route($link['route']) }}"
                       @click="mobileOpen = false"
                       x-show="mobileOpen"
                       x-transition:enter="transition ease-out duration-500"
                       x-transition:enter-start="opacity-0 translate-x-8"
                       x-transition:enter-end="opacity-100 translate-x-0"
                       style="transition-delay: {{ $i * 60 }}ms"
                       class="flex items-baseline gap-4 border-b border-zinc-100 py-4 dark:border-white/5">
                        <span class="font-mono text-xs text-indigo-400 dark:text-indigo-400/80">0{{ $i + 1 }}</span>
                        <span class="text-3xl font-bold tracking-tight {{ request()->routeIs($link['route']) ? 'text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-violet-500 to-cyan-400' : 'text-zinc-900 dark:text-white' }}">
                            {{ $link['label'] }}
                        </span>
                    </a>
                @endforeach
            </nav>

            <div class="relative mt-8 space-y-6">
                <a href="{{ route('contact.create') }}" @click="mobileOpen = false" class="block rounded-full bg-zinc-900 px-6 py-4 text-center text-base font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                    Contact
                </a>

                <div class="flex items-center justify-center gap-3">
                    @if ($settings->github_url)
                        <a href="{{ $settings->github_url }}" target="_blank" rel="noopener" aria-label="GitHub" class="grid size-11 place-items-center rounded-full border border-zinc-200 text-zinc-500 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/10 dark:text-zinc-400"><x-brand-icon name="github" class="size-4" /></a>
                    @endif
                    @if ($settings->linkedin_url)
                        <a href="{{ $settings->linkedin_url }}" target="_blank" rel="noopener" aria-label="LinkedIn" class="grid size-11 place-items-center rounded-full border border-zinc-200 text-zinc-500 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/10 dark:text-zinc-400"><x-brand-icon name="linkedin" class="size-4" /></a>
                    @endif
                </div>

                <p class="text-center text-xs text-zinc-400 dark:text-zinc-600">&copy; {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</header>
