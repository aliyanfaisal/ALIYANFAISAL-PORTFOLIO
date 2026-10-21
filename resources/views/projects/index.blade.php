@php($projects = config('research.projects'))
<x-layouts.app title="My Own Projects" description="Applied AI projects by Aliyan Faisal: InvoiceInspect, Cuelara and ManaJet.">
    <h1 class="mt-8 font-serif text-3xl font-bold">My Own Projects</h1>
    <p class="mt-3">Products and systems I have designed and built myself. I have also delivered many client projects as a freelancer, which are not listed here. Each page describes the problem, the approach and what it taught me. Written reports are listed under <a href="{{ route('reports') }}">Reports</a>.</p>

    <div class="mt-8 space-y-8">
        @foreach ($projects as $slug => $project)
            <article class="border-b border-neutral-200 pb-8">
                <h2 class="font-serif text-xl font-bold"><a href="{{ route('projects.show', $slug) }}" class="text-neutral-900">{{ $project['name'] }}</a></h2>
                <p class="text-sm text-neutral-600">{{ $project['role'] }} · {{ $project['status'] }}</p>
                <p class="mt-2">{{ $project['summary'] }}</p>
                <p class="mt-2 text-sm text-neutral-600">{{ implode(' · ', $project['stack']) }}</p>
                <p class="mt-2 text-sm"><a href="{{ route('projects.show', $slug) }}">Read more</a>@if ($project['url']) · <a href="{{ $project['url'] }}" target="_blank" rel="noopener">{{ parse_url($project['url'], PHP_URL_HOST) }}</a>@endif @if (! empty($project['repo'])) · <a href="{{ $project['repo'] }}" target="_blank" rel="noopener">GitHub</a>@endif @foreach ($project['report_links'] ?? [] as [$linkTitle, $linkUrl]) · <a href="{{ $linkUrl }}" target="_blank" rel="noopener">{{ $linkTitle }}</a>@endforeach</p>
            </article>
        @endforeach

    </div>
</x-layouts.app>
