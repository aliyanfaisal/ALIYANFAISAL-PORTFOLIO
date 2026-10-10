<x-layouts.app title="Contact — Aliyan Faisal">
    <section class="mx-auto max-w-5xl px-6 py-16">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400">Contact</p>
            <h1 class="mt-3 text-4xl font-bold text-zinc-900 dark:text-white">Get in Touch</h1>
            <p class="mx-auto mt-4 max-w-xl text-lg text-zinc-600 dark:text-zinc-400">
                Questions, feedback or ideas to collaborate on? Fill out the form or reach me directly below.
            </p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-10 md:grid-cols-5">
            <div class="space-y-4 md:col-span-2">
                <a href="mailto:contact@aliyanfaisal.com" class="flex items-center gap-3 rounded-2xl border border-zinc-200 bg-white p-4 transition hover:border-indigo-400/50 dark:border-white/10 dark:bg-zinc-900">
                    <span class="grid size-10 shrink-0 place-items-center rounded-full bg-indigo-500/10 text-indigo-500 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M22 6l-10 7L2 6"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">Email</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">contact@aliyanfaisal.com</p>
                    </div>
                </a>

                <div class="flex items-center gap-3 rounded-2xl border border-zinc-200 bg-white p-4 dark:border-white/10 dark:bg-zinc-900">
                    <span class="grid size-10 shrink-0 place-items-center rounded-full bg-zinc-500/10 text-zinc-500 dark:text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">Location</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Islamabad, Pakistan</p>
                    </div>
                </div>

                <a href="{{ config('seo.freelance_url') }}" rel="noopener" class="flex items-center gap-3 rounded-2xl border border-dashed border-indigo-400/50 bg-indigo-500/5 p-4 transition hover:border-indigo-400 dark:bg-indigo-500/10">
                    <span class="grid size-10 shrink-0 place-items-center rounded-full bg-indigo-500/10 text-indigo-500 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100">Freelance &amp; client projects</p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">See {{ parse_url(config('seo.freelance_url'), PHP_URL_HOST) }}</p>
                    </div>
                </a>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <a href="https://www.linkedin.com/in/aliyan-faisal-5162261b7/" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-full border border-zinc-300 px-4 py-2 text-center text-sm font-medium text-zinc-700 hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-200"><x-brand-icon name="linkedin" class="size-4" /> LinkedIn</a>
                    <a href="https://github.com/aliyanfaisal" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-full border border-zinc-300 px-4 py-2 text-center text-sm font-medium text-zinc-700 hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-200"><x-brand-icon name="github" class="size-4" /> GitHub</a>
                </div>
            </div>

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 md:col-span-3 dark:border-white/10 dark:bg-zinc-900">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-white/15 dark:text-white">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-white/15 dark:text-white">
                        @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Subject</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                           class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-white/15 dark:text-white">
                    @error('subject') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Message</label>
                    <textarea name="message" id="message" rows="5" required
                              class="mt-1.5 w-full rounded-lg border border-zinc-300 bg-transparent px-3 py-2 text-sm text-zinc-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-white/15 dark:text-white">{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full rounded-full bg-zinc-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                    Send Message
                </button>
            </form>
        </div>
    </section>
</x-layouts.app>
