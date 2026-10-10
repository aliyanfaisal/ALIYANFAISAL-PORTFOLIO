<x-layouts.app title="Page Not Found — Aliyan Faisal" robots="noindex, follow" :full-bleed="true">
    <section class="glow-gradient relative flex min-h-[calc(100vh-5rem)] items-center overflow-hidden pt-20">
        <div class="bg-grid absolute inset-0 -z-10"></div>

        <div class="pointer-events-none absolute inset-0 -z-10 hidden overflow-hidden text-indigo-400 dark:text-indigo-300 sm:block" aria-hidden="true">
            <x-icon name="code" class="animate-float absolute left-[10%] top-[20%] size-7 opacity-40" style="animation-delay: 0.2s" />
            <x-icon name="bolt" class="animate-float absolute right-[12%] top-[16%] size-6 opacity-35" style="animation-delay: 1.6s" />
            <x-icon name="cube" class="animate-float absolute left-[18%] top-[72%] size-6 opacity-35" style="animation-delay: 2.4s" />
            <x-icon name="sparkles" class="animate-float absolute right-[16%] top-[70%] size-7 opacity-40" style="animation-delay: 3.1s" />
        </div>

        <div class="mx-auto max-w-2xl px-6 py-16 text-center">
            <p class="text-gradient text-8xl font-bold tracking-tight sm:text-9xl">404</p>

            <h1 class="mt-4 text-3xl font-bold text-zinc-900 sm:text-4xl dark:text-white">
                This route doesn&rsquo;t exist.
            </h1>
            <p class="mx-auto mt-4 max-w-md text-lg text-zinc-600 dark:text-zinc-400">
                The page you&rsquo;re looking for was moved, renamed, or never built. Let&rsquo;s get you back on track.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('home') }}" class="rounded-full bg-zinc-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                    Back to Home
                </a>
                <a href="{{ route('blog.index') }}" class="rounded-full border border-zinc-300 px-6 py-3 text-sm font-semibold text-zinc-700 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-200">
                    Read the Blog
                </a>
            </div>

            <div class="glass-card mx-auto mt-12 flex max-w-md flex-wrap items-center justify-center gap-x-6 gap-y-2 px-6 py-4 text-sm">
                <a href="{{ route('projects.index') }}" class="font-medium text-zinc-600 transition hover:text-indigo-500 dark:text-zinc-300 dark:hover:text-indigo-400">Projects</a>
                <a href="{{ route('about') }}" class="font-medium text-zinc-600 transition hover:text-indigo-500 dark:text-zinc-300 dark:hover:text-indigo-400">About</a>
                <a href="{{ route('contact.create') }}" class="font-medium text-zinc-600 transition hover:text-indigo-500 dark:text-zinc-300 dark:hover:text-indigo-400">Contact</a>
            </div>
        </div>
    </section>
</x-layouts.app>
