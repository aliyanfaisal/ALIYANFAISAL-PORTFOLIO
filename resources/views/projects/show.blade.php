<x-layouts.app :title="$project['name']" :description="\Illuminate\Support\Str::limit($project['summary'], 157)">
    <p class="mt-6 text-sm"><a href="{{ route('projects.index') }}">&larr; All projects</a></p>

    <h1 class="mt-3 font-serif text-3xl font-bold">{{ $project['name'] }}</h1>
    <p class="mt-1 text-lg">{{ $project['tagline'] }}</p>
    <p class="mt-1 text-sm text-neutral-600">{{ $project['role'] }} · {{ $project['period'] }} · {{ $project['status'] }}</p>

    <p class="mt-3 text-sm">
        @if ($project['url'])<a href="{{ $project['url'] }}" rel="noopener">{{ parse_url($project['url'], PHP_URL_HOST) }}</a>@endif
        @if (! empty($project['repo']))@if ($project['url']) · @endif<a href="{{ $project['repo'] }}" rel="noopener">Repository</a>@endif
    </p>

    @if ($project['image'] && file_exists(public_path($project['image'])))
        <figure class="mt-6">
            <img src="{{ asset($project['image']) }}" alt="Screenshot of {{ $project['name'] }}" class="w-full border border-neutral-300" loading="lazy">
            <figcaption class="mt-1 text-xs text-neutral-500">Screenshot of {{ $project['name'] }}.</figcaption>
        </figure>
    @endif

    <x-section title="Summary"><p>{{ $project['summary'] }}</p></x-section>
    <x-section title="Problem"><p>{{ $project['problem'] }}</p></x-section>

    <x-section title="What it does">
        <dl class="space-y-3">
            @foreach ($project['what'] as [$heading, $text])
                <div>
                    <dt class="font-semibold">{{ $heading }}</dt>
                    <dd>{{ $text }}</dd>
                </div>
            @endforeach
        </dl>
    </x-section>

    <x-section title="Approach">
        <div class="space-y-3">
            @foreach ($project['approach'] as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
    </x-section>

    @isset($project['evaluation'])
        <x-section :title="$project['evaluation']['title']">
            <p class="text-sm text-neutral-600">{{ $project['evaluation']['note'] }}</p>
            <table class="mt-3 w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="border-b border-neutral-400"><th class="py-1 pr-3 font-semibold">Measure</th><th class="py-1 pr-3 font-semibold">Result</th><th class="py-1 font-semibold">Setting</th></tr>
                </thead>
                <tbody>
                    @foreach ($project['evaluation']['metrics'] as [$measure, $value, $setting])
                        <tr class="border-b border-neutral-200"><td class="py-1.5 pr-3">{{ $measure }}</td><td class="py-1.5 pr-3 font-semibold">{{ $value }}</td><td class="py-1.5 text-neutral-600">{{ $setting }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </x-section>
    @endisset

    @isset($project['planned'])
        <x-section title="Planned"><p>{{ $project['planned'] }}</p></x-section>
    @endisset

    @isset($project['limits'])
        <x-section title="Limits"><p>{{ $project['limits'] }}</p></x-section>
    @endisset

    <x-section title="Built with"><p>{{ implode(' · ', $project['stack']) }}</p></x-section>

    <x-section title="Why it matters to my research"><p>{{ $project['research'] }}</p></x-section>

    <x-section title="Reports">
        @if ($slug === 'invoiceinspect')
            <p>The evaluation results above come from my evaluation harness. A full written report is listed on the <a href="{{ route('reports') }}">Reports</a> page when it is ready.</p>
        @endif
        @foreach ($project['reports'] as $line)
            <p>{{ $line }}</p>
        @endforeach
        @if ($slug !== 'invoiceinspect' && empty($project['reports']))
            <p class="text-neutral-500">Coming soon.</p>
        @endif
    </x-section>
</x-layouts.app>
