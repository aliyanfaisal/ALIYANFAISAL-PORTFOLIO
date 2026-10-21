@php
    $edu = config('research.education');
    $ach = config('research.achievements');
    $hasTranscript = file_exists(public_path($edu['transcript_file']));
    // Voluntary roles are listed under "Volunteering", not under professional experience.
    [$volunteering, $jobs] = collect(config('research.experience'))->partition(fn ($job) => str_contains($job['role'], 'Voluntary'));
@endphp
<x-layouts.app title="Achievements" description="Academic record, research-relevant work, experience and recognition of Aliyan Faisal.">
    <h1 class="mt-8 font-serif text-3xl font-bold">Achievements</h1>
    <p class="mt-3">Academic record, research-relevant work, professional experience and other recognition.</p>

    <x-section title="Academic">
        <ul class="space-y-2">
            <li><span class="font-semibold">{{ $edu['rank'] }}.</span> {{ $edu['school'] }}, {{ $edu['years'] }}.@if ($hasTranscript) Semester-wise CGPA in the <a href="{{ asset($edu['transcript_file']) }}" target="_blank" rel="noopener">transcript (PDF)</a>.@endif</li>
            <li><span class="font-semibold">CGPA {{ $edu['cgpa'] }}</span>, {{ $edu['degree'] }}.</li>
            <li><span class="font-semibold">{{ $edu['scholarship'] }}.</span></li>
        </ul>
    </x-section>

    <x-section title="Research-relevant work">
        <ul class="space-y-2">
            <li><a href="{{ route('projects.show', 'invoiceinspect') }}" class="font-semibold">InvoiceInspect</a>: a published <a href="{{ route('reports') }}">research report and evaluation reports</a>.</li>
            <li><a href="{{ route('projects.show', 'manajet') }}" class="font-semibold">ManaJet</a>: final year project, supervised by Dr. Sajid Hussain, Karakoram International University.</li>
            <li><a href="{{ route('open-source.index') }}" class="font-semibold">Open source</a> work.</li>
        </ul>
    </x-section>

    <x-section title="Professional experience">
        <ul class="space-y-2">
            @foreach ($jobs as $job)
                <li><span class="font-semibold">{{ $job['role'] }}</span>, {{ $job['org'] }} ({{ $job['years'] }}).</li>
            @endforeach
            <li><span class="font-semibold">{{ $ach['freelance']['reviews'] }}</span> {{ $ach['freelance']['text'] }}</li>
        </ul>
        <p class="mt-3 text-sm"><a href="{{ route('cv') }}">Full CV</a></p>
    </x-section>

    <x-section title="Volunteering and school recognition">
        <ul class="space-y-2">
            @foreach ($volunteering as $job)
                <li><span class="font-semibold">Volunteering work: {{ trim(str_replace('(Voluntary Work)', '', $job['role'])) }}, {{ $job['org'] }} ({{ $job['years'] }}).</span> <a href="{{ route('voluntary') }}">Details</a></li>
            @endforeach
            @foreach ($ach['other'] as [$title, $text, $file])
                <li><span class="font-semibold">{{ $title }}.</span> {{ $text }}@if ($file && file_exists(public_path($file))) <a href="{{ asset($file) }}" target="_blank" rel="noopener">Certificate (image)</a>@endif</li>
            @endforeach
        </ul>
    </x-section>
</x-layouts.app>
