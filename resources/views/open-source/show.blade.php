<x-layouts.app :title="$item['name']" :description="\Illuminate\Support\Str::limit($item['summary'], 157)">
    <p class="mt-6 text-sm"><a href="{{ route('open-source.index') }}">&larr; Open source</a></p>

    <h1 class="mt-3 font-serif text-3xl font-bold">{{ $item['name'] }}</h1>
    <p class="mt-1 text-lg">{{ $item['tagline'] }}</p>
    <p class="mt-1 text-sm text-neutral-600">{{ $item['status'] }} · v{{ $item['version'] }} · {{ $item['requires'] }}</p>
    <p class="mt-3 text-sm"><a href="{{ $item['repo'] }}" rel="noopener">Repository on GitHub</a></p>

    <x-section title="Summary"><p>{{ $item['summary'] }}</p></x-section>

    <x-section title="Key points">
        <dl class="space-y-3">
            @foreach ($item['highlights'] as [$heading, $text])
                <div><dt class="font-semibold">{{ $heading }}</dt><dd>{{ $text }}</dd></div>
            @endforeach
        </dl>
    </x-section>

    <x-section title="Install">
        @foreach ($item['install'] as $command)
            <pre class="mt-2 overflow-x-auto border border-neutral-300 bg-neutral-50 p-3 font-mono text-sm"><code>{{ $command }}</code></pre>
        @endforeach
    </x-section>

    <x-section title="Commands">
        <table class="w-full border-collapse text-left text-sm">
            <tbody>
                @foreach ($item['commands'] as [$command, $text])
                    <tr class="border-b border-neutral-200"><td class="py-1.5 pr-4 font-mono">{{ $command }}</td><td class="py-1.5">{{ $text }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </x-section>

    <x-section title="How it works">
        <div class="space-y-3">
            @foreach ($item['how'] as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
    </x-section>

    <x-section title="Where it is headed">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($item['roadmap'] as $line)
                <li>{{ $line }}</li>
            @endforeach
        </ul>
    </x-section>
</x-layouts.app>
