@props(['anchor' => 'newsletter'])
@if (session('newsletter_pending_email') && session('newsletter_form', 'newsletter') === $anchor)
    <form action="{{ route('newsletter.verify') }}" method="POST" class="mt-3">
        @csrf
        <input type="hidden" name="email" value="{{ session('newsletter_pending_email') }}">
        <input type="hidden" name="newsletter_form" value="{{ $anchor }}">
        <p class="mb-2 text-xs text-zinc-500 dark:text-zinc-400">Code sent to {{ session('newsletter_pending_email') }}</p>
        <div class="flex gap-2">
            <label for="{{ $anchor }}-code" class="sr-only">6-digit code</label>
            <input
                type="text"
                id="{{ $anchor }}-code"
                name="code"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="6"
                pattern="[0-9]{6}"
                placeholder="123456"
                required
                autofocus
                class="w-full min-w-0 rounded-full border border-zinc-300 bg-white px-4 py-2 text-sm tracking-widest text-zinc-700 placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none dark:border-white/15 dark:bg-zinc-900 dark:text-zinc-200"
            >
            <button type="submit" class="shrink-0 rounded-full bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                Confirm
            </button>
        </div>
        @error('code', 'newsletter')
            <p class="mt-2 text-xs text-rose-500">{{ $message }}</p>
        @enderror
    </form>
@else
<form action="{{ route('newsletter.store') }}" method="POST" class="mt-3">
    @csrf
    <input type="hidden" name="newsletter_form" value="{{ $anchor }}">
    <div class="flex gap-2">
        <label for="{{ $anchor }}-email" class="sr-only">Email address</label>
        <input
            type="email"
            id="{{ $anchor }}-email"
            name="email"
            value="{{ old('newsletter_form') === $anchor ? old('email') : '' }}"
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
        @if (old('newsletter_form') === $anchor)<p class="mt-2 text-xs text-rose-500">{{ $message }}</p>@endif
    @enderror
</form>
@endif
