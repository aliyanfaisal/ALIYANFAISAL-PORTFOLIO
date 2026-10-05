@php
    $servicesTitle = 'AI, LLM Integration & Full-Stack Development Services — Aliyan Faisal';
    $servicesDescription = config('seo.descriptions.services');
    $graph = [
        \App\Support\Seo\Schema::personLite(),
        \App\Support\Seo\Schema::collectionPage(route('services.index'), $servicesTitle, $servicesDescription, route('services.index').'#breadcrumb'),
        \App\Support\Seo\Schema::breadcrumbs([['name' => 'Home', 'url' => route('home')], ['name' => 'Services', 'url' => route('services.index')]], route('services.index')),
        ...\App\Support\Seo\Schema::services($services),
    ];
@endphp
<x-layouts.app :title="$servicesTitle" :description="$servicesDescription" :graph="$graph">
    <section class="mx-auto max-w-4xl px-6 py-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400">Services</p>
        <h1 class="mt-3 text-4xl font-bold text-zinc-900 dark:text-white">LLM Integration, AI Automation &amp; Full-Stack Development Services</h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
            Hire a full-stack developer and AI/LLM systems engineer — Fiverr Level 2 Seller, 5.0 rating across 200+ reviews. Pick a service below or
            <a href="{{ route('contact.create') }}" class="font-medium text-indigo-500 hover:underline dark:text-indigo-400">get in touch</a> for something custom.
        </p>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-20">
        @php $topRatingCount = $services->max('rating_count'); @endphp
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                <x-service-card :service="$service" :bestSeller="$service->rating_count === $topRatingCount && $topRatingCount > 0" heading="h2" />
            @endforeach
        </div>
    </section>
</x-layouts.app>
