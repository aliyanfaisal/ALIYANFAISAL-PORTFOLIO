@props([
    'title' => null,
    'description' => null,
    'fullBleed' => false,
    'canonical' => null,
    'image' => null,
    'ogType' => 'website',
    'robots' => null,
    'graph' => [],
])
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo')

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    {{ $head ?? '' }}

    <script>
        (function () {
            var theme = localStorage.getItem('theme') || 'dark';
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>

    @production
        <!-- Cloudflare Web Analytics -->
        <script type='module' src='https://static.cloudflareinsights.com/beacon.min.js'
            data-cf-beacon='{"token": "abd1fd4bee9c4bbe901abeab02da68bb"}'></script>
        <!-- End Cloudflare Web Analytics -->
    @endproduction

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen flex-col bg-white text-zinc-700 dark:bg-zinc-950 dark:text-zinc-300">
    @include('partials.navbar')

    <main class="flex-1 @if(!Request::is('/') && !$fullBleed) {{ "pt-20" }} @endif">
        @if (session('status'))
            <div
                class="mx-auto mt-6 max-w-3xl rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-600 dark:text-emerald-400">
                {{ session('status') }}
            </div>
        @endif

        {{ $slot }}
    </main>

    @include('partials.footer')
</body>

</html>