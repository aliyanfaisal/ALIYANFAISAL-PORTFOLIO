@php
    $interests = config('research.interests');
    $projects = config('research.projects');
    $openSource = config('research.open_source');
    $edu = config('research.education');
@endphp
<x-layouts.app description="AI engineer and aspiring researcher with a BSc in IT, applying to research-oriented MSc programs in AI and ML. Research interests: LLM reliability and evaluation, calibration, small and efficient language models, and AI for low-resource languages.">
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
            @foreach (array_filter($interests, fn ($interest) => ! empty($interest['related'])) as $interest)
                <li>{{ $interest['name'] }}</li>
            @endforeach
        </ul>
        <p class="mt-3 text-sm"><a href="{{ route('research') }}">See all my research interests</a></p>
    </x-section>

    <x-section title="My own projects">
        <ul class="space-y-3">
            @foreach ($projects as $slug => $project)
                <li>
                    <a href="{{ route('projects.show', $slug) }}" class="font-semibold">{{ $project['name'] }}</a><br>
                    <span>{{ $project['blurb'] }}</span>
                    @if ($project['url'] || ! empty($project['repo']))
                        <br><span class="text-sm">
                            @if ($project['url'])<a href="{{ $project['url'] }}" rel="noopener">{{ parse_url($project['url'], PHP_URL_HOST) }}</a>@endif
                            @if (! empty($project['repo']))@if ($project['url']) · @endif<a href="{{ $project['repo'] }}" rel="noopener">GitHub</a>@endif
                        </span>
                    @endif
                    @if (! empty($project['report_links']))
                        <br><span class="text-sm">Reports: @foreach ($project['report_links'] as [$linkTitle, $linkUrl])<a href="{{ $linkUrl }}" rel="noopener">{{ $linkTitle }}</a>@if (! $loop->last) · @endif @endforeach</span>
                    @endif
                </li>
            @endforeach
        </ul>
        <p class="mt-3 text-sm"><a href="{{ route('projects.index') }}">All my own projects</a></p>
    </x-section>

    <x-section title="Open source">
        <ul class="space-y-3">
            @foreach ($openSource as $slug => $item)
                <li>
                    <a href="{{ route('open-source.show', $slug) }}" class="font-semibold">{{ $item['name'] }}</a><br>
                    <span>{{ $item['tagline'] }}</span><br>
                    <span class="text-sm"><a href="{{ $item['repo'] }}" rel="noopener">GitHub</a></span>
                </li>
            @endforeach
        </ul>
    </x-section>

    <x-section title="Technical skills">
        <ul class="space-y-2">
            @foreach (config('research.skills') as [$group, $items])
                <li><span class="font-semibold">{{ $group }}:</span> {{ $items }}.</li>
            @endforeach
        </ul>
    </x-section>

    <x-section title="Education">
        <p><span class="font-semibold">{{ $edu['school'] }}</span>, {{ $edu['place'] }}</p>
        <p>{{ $edu['degree'] }} · CGPA {{ $edu['cgpa'] }} · {{ $edu['years'] }}</p>
        <p>{{ $edu['rank'] }}</p>
        <p>{{ $edu['scholarship'] }}</p>
        <p class="mt-2 text-sm"><a href="{{ route('cv') }}">Education, experience and skills</a></p>
    </x-section>
</x-layouts.app>
