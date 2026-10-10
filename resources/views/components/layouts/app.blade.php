@props(['title' => null, 'description' => null])
@php
    $person = config('research.person');
    $pageTitle = $title ? $title.' — '.$person['name'] : $person['name'].' — Research portfolio';
    $pageDescription = $description ?? 'Research portfolio of '.$person['name'].': applied LLMs, retrieval-augmented generation and data-centric systems.';
    $nav = [
        ['Home', 'home', 'home'],
        ['Research', 'research', 'research'],
        ['Projects', 'projects.index', 'projects*'],
        ['Open source', 'open-source.index', 'open-source*'],
        ['Reports', 'reports', 'reports'],
        ['CV', 'cv', 'cv'],
        ['Contact', 'contact', 'contact'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary">
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'Person', 'name' => $person['name'], 'url' => url('/'), 'jobTitle' => 'Software engineer', 'email' => $person['email'], 'sameAs' => [$person['github'], $person['linkedin']], 'alumniOf' => config('research.education.school')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <div class="mx-auto max-w-3xl px-5 py-8 sm:py-12">
        <header class="border-b border-neutral-300 pb-4">
            <a href="{{ route('home') }}" class="font-serif text-2xl font-bold text-neutral-900 no-underline hover:underline">{{ $person['name'] }}</a>
            <nav class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-sm" aria-label="Main">
                @foreach ($nav as [$label, $route, $pattern])
                    <a href="{{ route($route) }}" @class(['no-underline hover:underline', 'font-semibold text-neutral-900' => request()->routeIs($pattern)]) @if (request()->routeIs($pattern)) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
        </header>

        <main class="pt-2">
            {{ $slot }}
        </main>

        <footer class="mt-14 border-t border-neutral-300 pt-4 text-sm text-neutral-600">
            <p>
                <a href="mailto:{{ $person['email'] }}">{{ $person['email'] }}</a>
                · <a href="{{ $person['github'] }}" rel="noopener">GitHub</a>
                · <a href="{{ $person['linkedin'] }}" rel="noopener">LinkedIn</a>
            </p>
            <p class="mt-1">&copy; {{ date('Y') }} {{ $person['name'] }}</p>
        </footer>
    </div>
</body>
</html>
