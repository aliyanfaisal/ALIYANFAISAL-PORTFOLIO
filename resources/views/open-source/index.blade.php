@php($items = config('research.open_source'))
<x-layouts.app title="Open source" description="Open-source projects by Aliyan Faisal, including Notemind, a Claude Code plugin for notes and reminders.">
    <h1 class="mt-8 font-serif text-3xl font-bold">Open source</h1>
    <p class="mt-3">Small tools I build and share publicly. Each has its own repository.</p>

    <div class="mt-8 space-y-8">
        @foreach ($items as $slug => $item)
            <article class="border-b border-neutral-200 pb-8">
                <h2 class="font-serif text-xl font-bold"><a href="{{ route('open-source.show', $slug) }}" class="text-neutral-900">{{ $item['name'] }}</a></h2>
                <p class="text-sm text-neutral-600">{{ $item['status'] }} · v{{ $item['version'] }}</p>
                <p class="mt-2">{{ $item['summary'] }}</p>
                <p class="mt-2 text-sm"><a href="{{ route('open-source.show', $slug) }}">Read more</a> · <a href="{{ $item['repo'] }}" rel="noopener">Repository</a></p>
            </article>
        @endforeach
        <p class="text-neutral-500">{{ config('research.coming_soon.open_source') }}</p>
    </div>
</x-layouts.app>
