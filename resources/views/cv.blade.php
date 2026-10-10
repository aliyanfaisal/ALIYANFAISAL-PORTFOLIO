@php
    $edu = config('research.education');
@endphp
<x-layouts.app title="CV" description="Education, experience and skills of Aliyan Faisal: BSc in Information Technology (CGPA 3.80), software and applied AI engineering.">
    <h1 class="mt-8 font-serif text-3xl font-bold">Curriculum vitae</h1>
    <p class="mt-3 text-sm">
        @if ($hasCv)<a href="{{ route('cv.file') }}">Download CV (PDF)</a>@else<span class="text-neutral-500">CV (PDF): coming soon</span>@endif
    </p>

    <x-section title="Education">
        <p class="flex flex-wrap justify-between gap-x-4"><span class="font-semibold">{{ $edu['school'] }}</span><span>{{ $edu['place'] }}</span></p>
        <p class="flex flex-wrap justify-between gap-x-4"><span class="italic">{{ $edu['degree'] }}</span><span>CGPA {{ $edu['cgpa'] }} · {{ $edu['years'] }}</span></p>
        <p class="mt-2"><span class="font-semibold">Scholarship:</span> {{ $edu['scholarship'] }}.</p>
        <p class="mt-1"><span class="font-semibold">Coursework:</span> {{ implode(', ', $edu['coursework']) }}.</p>
        <p class="mt-1"><span class="font-semibold">Focus:</span> {{ $edu['focus'] }}</p>
    </x-section>

    <x-section title="Experience">
        <div class="space-y-5">
            @foreach (config('research.experience') as $job)
                <div>
                    <p class="flex flex-wrap justify-between gap-x-4"><span class="font-semibold">{{ $job['org'] }}</span><span>{{ $job['place'] }}</span></p>
                    <p class="flex flex-wrap justify-between gap-x-4"><span class="italic">{{ $job['role'] }}</span><span>{{ $job['years'] }}</span></p>
                    <p class="mt-1">{{ $job['text'] }}</p>
                </div>
            @endforeach
        </div>
    </x-section>

    <x-section title="Technical skills">
        <ul class="space-y-2">
            @foreach (config('research.skills') as [$group, $items])
                <li><span class="font-semibold">{{ $group }}:</span> {{ $items }}.</li>
            @endforeach
        </ul>
    </x-section>

    <x-section title="Languages"><p>{{ config('research.languages') }}</p></x-section>
</x-layouts.app>
