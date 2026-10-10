@php($interests = config('research.interests'))
<x-layouts.app title="Research interests" description="Research interests: prompt engineering and token efficiency, retrieval-augmented generation, LLM reliability and evaluation, multimodal document understanding and data-centric systems.">
    <h1 class="mt-8 font-serif text-3xl font-bold">Research interests</h1>
    <p class="mt-3">
        I want to study applied LLM systems more rigorously than building them allows. The areas below come directly from problems I ran into
        while building the projects listed on this site. The questions are things I would like to investigate, not claims of results.
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

            <p class="mt-4 text-sm">
                <span class="font-semibold">Related work:</span>
                @foreach ($interest['related'] as $slug)
                    <a href="{{ route('projects.show', $slug) }}">{{ config("research.projects.$slug.name") }}</a>@unless ($loop->last), @endunless
                @endforeach
            </p>
        </x-section>
    @endforeach
</x-layouts.app>
