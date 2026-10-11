@php
    $interests = config('research.interests');
    $projects = config('research.projects');
    $openSource = config('research.open_source');
    $edu = config('research.education');
@endphp
<x-layouts.app description="AI engineer and aspiring researcher with a BSc in IT, applying to research-oriented MSc programs in AI and ML. Research interests: prompt engineering, retrieval-augmented generation, LLM reliability and data-centric systems.">
    <section class="mt-8">
        <h1 class="font-serif text-3xl font-bold leading-tight">{{ $person['name'] }}</h1>
        <p class="mt-1 text-neutral-600">{{ $person['title'] }} · {{ $person['location'] }}</p>

        @foreach ($person['summary'] as $paragraph)
            <p class="mt-4">{{ $paragraph }}</p>
        @endforeach

        @if ($hasCv)
            <p class="mt-4 text-sm"><a href="{{ route('cv.file') }}">Download CV (PDF)</a></p>
        @endif
    </section>

    <x-section title="Research interests">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($interests as $interest)
                <li>{{ $interest['name'] }}</li>
            @endforeach
        </ul>
        <p class="mt-3 text-sm"><a href="{{ route('research') }}">Read more about my research interests</a></p>
    </x-section>

    <x-section title="Selected projects">
        <ul class="space-y-3">
            @foreach ($projects as $slug => $project)
                <li>
                    <a href="{{ route('projects.show', $slug) }}" class="font-semibold">{{ $project['name'] }}</a><br>
                    <span>{{ $project['blurb'] }}</span>
                </li>
            @endforeach
            <li class="text-neutral-500">{{ config('research.coming_soon.projects') }}</li>
        </ul>
        <p class="mt-3 text-sm"><a href="{{ route('projects.index') }}">All projects</a> · <a href="{{ route('reports') }}">Reports</a></p>
    </x-section>

    <x-section title="Open source">
        <ul class="space-y-3">
            @foreach ($openSource as $slug => $item)
                <li>
                    <a href="{{ route('open-source.show', $slug) }}" class="font-semibold">{{ $item['name'] }}</a><br>
                    <span>{{ $item['tagline'] }}</span>
                </li>
            @endforeach
            <li class="text-neutral-500">{{ config('research.coming_soon.open_source') }}</li>
        </ul>
    </x-section>

    <x-section title="Education">
        <p><span class="font-semibold">{{ $edu['school'] }}</span>, {{ $edu['place'] }}</p>
        <p>{{ $edu['degree'] }} · CGPA {{ $edu['cgpa'] }} · {{ $edu['years'] }}</p>
        <p>{{ $edu['scholarship'] }}</p>
        <p class="mt-2 text-sm"><a href="{{ route('cv') }}">Education, experience and skills</a></p>
    </x-section>
</x-layouts.app>
