@php($projects = config('research.projects'))
<x-layouts.app title="Projects" description="Applied AI projects by Aliyan Faisal: Cuelara, InvoiceInspect, ManaJet and a provincial data dashboard.">
    <h1 class="mt-8 font-serif text-3xl font-bold">Projects</h1>
    <p class="mt-3">Systems I have designed and built. Each page describes the problem, the approach and what it taught me. Written reports are listed under <a href="{{ route('reports') }}">Reports</a>.</p>

    <div class="mt-8 space-y-8">
        @foreach ($projects as $slug => $project)
            <article class="border-b border-neutral-200 pb-8">
                <h2 class="font-serif text-xl font-bold"><a href="{{ route('projects.show', $slug) }}" class="text-neutral-900">{{ $project['name'] }}</a></h2>
                <p class="text-sm text-neutral-600">{{ $project['role'] }} · {{ $project['period'] }} · {{ $project['status'] }}</p>
                <p class="mt-2">{{ $project['summary'] }}</p>
                <p class="mt-2 text-sm text-neutral-600">{{ implode(' · ', $project['stack']) }}</p>
                <p class="mt-2 text-sm"><a href="{{ route('projects.show', $slug) }}">Read more</a></p>
            </article>
        @endforeach

        <p class="text-neutral-500">{{ config('research.coming_soon.projects') }}</p>
    </div>
</x-layouts.app>
