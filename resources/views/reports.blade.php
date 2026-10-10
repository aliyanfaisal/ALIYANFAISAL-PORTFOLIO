@php($reports = config('research.reports'))
<x-layouts.app title="Reports" description="Written reports and evaluation results for projects by Aliyan Faisal.">
    <h1 class="mt-8 font-serif text-3xl font-bold">Reports</h1>
    <p class="mt-3">Written reports for the projects on this site. Reports are added here as they are completed.</p>

    <div class="mt-8 space-y-6">
        @foreach ($reports as $report)
            @php($slug = $report['project'])
            <article class="border-b border-neutral-200 pb-6">
                <h2 class="font-serif text-lg font-bold">{{ $report['title'] }}</h2>
                <p class="text-sm text-neutral-600">Project: <a href="{{ route('projects.show', $slug) }}">{{ config("research.projects.$slug.name") }}</a></p>
                <p class="mt-1">{{ $report['summary'] }}</p>

                @if ($slug === 'invoiceinspect')
                    @php($metrics = config('research.projects.invoiceinspect.evaluation'))
                    <table class="mt-3 w-full border-collapse text-left text-sm">
                        <thead><tr class="border-b border-neutral-400"><th class="py-1 pr-3 font-semibold">Measure</th><th class="py-1 pr-3 font-semibold">Result</th><th class="py-1 font-semibold">Setting</th></tr></thead>
                        <tbody>
                            @foreach ($metrics['metrics'] as [$measure, $value, $setting])
                                <tr class="border-b border-neutral-200"><td class="py-1.5 pr-3">{{ $measure }}</td><td class="py-1.5 pr-3 font-semibold">{{ $value }}</td><td class="py-1.5 text-neutral-600">{{ $setting }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="mt-2 text-sm text-neutral-600">{{ $metrics['note'] }}</p>
                @endif

                <p class="mt-2 text-sm">
                    @if ($report['file'] && file_exists(public_path($report['file'])))
                        <a href="{{ asset($report['file']) }}">Download report (PDF)</a>
                    @else
                        <span class="text-neutral-500">Full report (PDF): coming soon</span>
                    @endif
                </p>
            </article>
        @endforeach
    </div>
</x-layouts.app>
