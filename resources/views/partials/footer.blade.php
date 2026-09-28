@php
    $settings = \App\Models\Setting::current();
@endphp
<footer class="border-t border-zinc-200 dark:border-white/10">
    <div class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-2 sm:gap-10 lg:grid-cols-4">
            <div class="col-span-2 sm:col-span-1">
                <a href="{{ route('home') }}"
                    class="text-lg font-semibold tracking-tight text-zinc-900 dark:text-white">
                    {{ strtoupper($settings->site_name) }}<span class="text-gradient">.</span>
                </a>
                <p class="mt-3 max-w-xs text-sm text-zinc-500 dark:text-zinc-400">
                    Full-stack developer building Laravel, WordPress and AI-powered web applications for clients
                    worldwide.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Navigate</h3>
                <ul class="mt-3 space-y-2 text-sm text-zinc-500 dark:text-zinc-400">
                    <li><a href="{{ route('about') }}"
                            class="hover:text-indigo-500 dark:hover:text-indigo-400">About</a></li>
                    <li><a href="{{ route('projects.index') }}"
                            class="hover:text-indigo-500 dark:hover:text-indigo-400">Projects</a></li>
                    <li><a href="{{ route('services.index') }}"
                            class="hover:text-indigo-500 dark:hover:text-indigo-400">Services</a></li>
                    <li><a href="{{ route('contact.create') }}"
                            class="hover:text-indigo-500 dark:hover:text-indigo-400">Contact</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Connect</h3>
                <ul class="mt-3 space-y-2 text-sm text-zinc-500 dark:text-zinc-400">
                    @if ($settings->github_url)
                        <li><a href="{{ $settings->github_url }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 hover:text-indigo-500 dark:hover:text-indigo-400"><x-brand-icon
                                    name="github" class="size-4" /> GitHub</a></li>
                    @endif
                    @if ($settings->fiverr_url)
                        <li><a href="{{ $settings->fiverr_url }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 hover:text-indigo-500 dark:hover:text-indigo-400"><x-brand-icon
                                    name="fiverr" class="size-4" /> Fiverr</a></li>
                    @endif
                    @if ($settings->upwork_url)
                        <li><a href="{{ $settings->upwork_url }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 hover:text-indigo-500 dark:hover:text-indigo-400"><x-brand-icon
                                    name="upwork" class="size-4" /> Upwork</a></li>
                    @endif
                    @if ($settings->linkedin_url)
                        <li><a href="{{ $settings->linkedin_url }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 hover:text-indigo-500 dark:hover:text-indigo-400"><x-brand-icon
                                    name="linkedin" class="size-4" /> LinkedIn</a></li>
                    @endif
                    @if ($settings->contact_email)
                        <li><a href="mailto:{{ $settings->contact_email }}"
                                class="inline-flex items-start gap-2 break-all hover:text-indigo-500 dark:hover:text-indigo-400"><x-icon
                                    name="envelope" class="size-4 shrink-0 translate-y-0.5" /> {{ $settings->contact_email }}</a></li>
                    @endif
                </ul>
            </div>

            <div class="col-span-2 sm:col-span-1">
                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Newsletter</h3>
                <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Get new articles in your inbox. No spam.</p>
                <form action="{{ route('newsletter.store') }}" method="POST" class="mt-3">
                    @csrf
                    <div class="flex gap-2">
                        <label for="footer-newsletter-email" class="sr-only">Email address</label>
                        <input
                            type="email"
                            id="footer-newsletter-email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            required
                            class="w-full min-w-0 rounded-full border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-700 placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none dark:border-white/15 dark:bg-zinc-900 dark:text-zinc-200"
                        >
                        <button
                            type="submit"
                            class="shrink-0 rounded-full bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400"
                        >
                            Subscribe
                        </button>
                    </div>
                    @error('email')
                        <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        </div>

        <div
            class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-zinc-200 pt-6 text-xs text-zinc-400 dark:border-white/10 sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ $settings->site_name }}. All rights reserved.</p>
            <a href="{{ route('privacy-policy') }}" class="hover:text-indigo-500 dark:hover:text-indigo-400">Privacy Policy</a>
        </div>
    </div>
</footer>