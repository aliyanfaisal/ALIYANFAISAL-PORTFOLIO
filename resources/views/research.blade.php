@php($interests = config('research.interests'))
<x-layouts.app title="Research interests" description="Research interests of Aliyan Faisal: LLM reliability and evaluation, uncertainty and calibration, small and efficient language models, token efficiency across languages, retrieval-augmented generation, learned versus rule-based information extraction, machine learning for prompt evaluation, and AI for climate resilience.">
    <h1 class="mt-8 font-serif text-3xl font-bold">Research interests</h1>
    <p class="mt-3">
        I want to study applied LLM systems more rigorously than building them allows. Most of the areas below come from problems I ran into
        while building the projects listed on this site; one is an area I have not built in yet and say so. The questions are things I would like to investigate, not claims of results.
    </p>

    @foreach ($interests as $interest)
        <x-section :title="$interest['name']">
            <p>{{ $interest['text'] }}</p>

            <p class="mt-4 text-sm font-semibold">Questions I am interested in</p>
            <ul class="mt-1 list-disc space-y-1 pl-5">
                @foreach ($interest['questions'] as $question)
                    <li>{{ $question }}</li>
                @endforeach
            </ul>

            @if (! empty($interest['related']))
                <p class="mt-4 text-sm">
                    <span class="font-semibold">Related work:</span>
                    @foreach ($interest['related'] as $slug)
                        @if (config("research.voluntary_work.$slug"))
                            <a href="{{ route('voluntary') }}">{{ config("research.voluntary_work.$slug.name") }}</a>@unless ($loop->last), @endunless
                        @else
                            <a href="{{ route('projects.show', $slug) }}">{{ config("research.projects.$slug.name") }}</a>@unless ($loop->last), @endunless
                        @endif
                    @endforeach
                </p>
            @endif
        </x-section>
    @endforeach

    <x-section title="Beyond these areas">
        <p>{{ config('research.interests_note') }}</p>
    </x-section>
</x-layouts.app>
