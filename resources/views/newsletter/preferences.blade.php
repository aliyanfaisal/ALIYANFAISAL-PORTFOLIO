<x-layouts.app title="Newsletter preferences — Aliyan Faisal" robots="noindex,nofollow">
    <section class="mx-auto max-w-xl px-6 py-16">
        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400">Newsletter</p>
        <h1 class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">Email preferences</h1>
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $subscriber->email }}</p>

        @if (session('status'))
            <p class="mt-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-600 dark:text-emerald-400">{{ session('status') }}</p>
        @endif

        @if (! $subscriber->isSubscribed())
            <p class="mt-6 text-zinc-600 dark:text-zinc-400">You're currently unsubscribed. Pick how often you'd like to hear from me to subscribe again.</p>
        @else
            <p class="mt-6 text-zinc-600 dark:text-zinc-400">How often would you like the best new posts in your inbox?</p>
        @endif

        <form method="POST" action="{{ route('newsletter.preferences.update', $subscriber->token) }}" class="mt-6 space-y-3">
            @csrf
            @method('PUT')
            @foreach (\App\Enums\NewsletterFrequency::cases() as $option)
                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-zinc-200 bg-white p-4 transition hover:border-indigo-400/50 has-[:checked]:border-indigo-500 dark:border-white/10 dark:bg-zinc-900">
                    <input type="radio" name="frequency" value="{{ $option->value }}" class="mt-1" @checked($subscriber->frequency === $option)>
                    <span>
                        <span class="block text-sm font-medium text-zinc-900 dark:text-white">{{ $option->label() }}</span>
                        <span class="block text-sm text-zinc-500 dark:text-zinc-400">{{ $option->description() }}</span>
                    </span>
                </label>
            @endforeach
            <div class="pt-4" x-data="{ topics: '{{ old('topics', count($selectedCategoryIds) ? 'selected' : 'all') }}' }">
                <p class="text-sm font-medium text-zinc-900 dark:text-white">Topics</p>
                <div class="mt-2 flex gap-4 text-sm text-zinc-700 dark:text-zinc-300">
                    <label class="flex items-center gap-2"><input type="radio" name="topics" value="all" x-model="topics"> All topics</label>
                    <label class="flex items-center gap-2"><input type="radio" name="topics" value="selected" x-model="topics"> Only some</label>
                </div>
                <div x-show="topics === 'selected'" x-cloak class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach ($categories as $category)
                        <label class="flex items-center gap-2 rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-300">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, old('categories', $selectedCategoryIds)))>
                            {{ $category->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            @error('frequency')
                <p class="text-xs text-rose-500">{{ $message }}</p>
            @enderror
            <button type="submit" class="rounded-full bg-zinc-900 px-5 py-2 text-sm font-medium text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                {{ $subscriber->isSubscribed() ? 'Save preference' : 'Subscribe again' }}
            </button>
        </form>

        @if ($subscriber->isSubscribed())
            <form method="POST" action="{{ route('newsletter.unsubscribe', $subscriber->token) }}" class="mt-10 border-t border-zinc-200 pt-6 dark:border-white/10">
                @csrf
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Not for you anymore?</p>
                <button type="submit" class="mt-2 text-sm font-medium text-rose-500 hover:underline">Unsubscribe from all emails</button>
            </form>
        @endif
    </section>
</x-layouts.app>
