@php
    $serviceDescription = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', (string) $service->description)), 157);
    $graph = [
        \App\Support\Seo\Schema::personLite(),
        ...\App\Support\Seo\Schema::services(collect([$service])),
        \App\Support\Seo\Schema::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Services', 'url' => route('services.index')],
            ['name' => \Illuminate\Support\Str::title($service->title), 'url' => route('services.show', $service)],
        ], route('services.show', $service)),
    ];
@endphp
<x-layouts.app :title="\Illuminate\Support\Str::title($service->title).' — Aliyan Faisal'" :description="$serviceDescription" :graph="$graph">
    <section class="mx-auto max-w-6xl px-6 py-12">
        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-zinc-500 hover:text-indigo-500 dark:text-zinc-400 dark:hover:text-indigo-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            Back to Services
        </a>

        <div class="mt-6 grid gap-12 lg:grid-cols-3">
            <div class="lg:col-span-2">
                @if ($service->category)
                    <span class="text-xs font-medium uppercase tracking-wide text-indigo-500 dark:text-indigo-400">{{ $service->category }}</span>
                @endif
                <h1 class="mt-2 text-3xl font-bold capitalize text-zinc-900 dark:text-white">{{ $service->title }}</h1>

                <div class="mt-3 flex items-center gap-2 text-sm">
                    <div class="flex items-center gap-1 text-amber-500">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118l-3.37-2.448c-.782-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.285-3.958z"/></svg>
                        <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ number_format((float) $service->rating, 2) }}</span>
                    </div>
                    <span class="text-zinc-400">({{ $service->rating_count }} reviews)</span>
                </div>

                @if ($service->image_url)
                    <div class="mt-6 aspect-video w-full overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100 dark:border-white/10 dark:bg-white/5">
                        <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="h-full w-full object-cover">
                    </div>
                @endif

                <div class="prose-body mt-8 whitespace-pre-line text-[15px] leading-relaxed">{{ $service->description }}</div>

                @if ($service->faqs->isNotEmpty())
                    <div class="mt-12">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Frequently Asked Questions</h2>
                        <div class="mt-4 divide-y divide-zinc-200 dark:divide-white/10" x-data="{ open: null }">
                            @foreach ($service->faqs as $faq)
                                <div class="py-4">
                                    <button type="button" @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}" class="flex w-full items-center justify-between text-left">
                                        <span class="font-medium text-zinc-800 dark:text-zinc-100">{{ $faq->question }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-zinc-400 transition" :class="open === {{ $faq->id }} && 'rotate-45'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                                    </button>
                                    <p x-show="open === {{ $faq->id }}" x-transition x-cloak class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">{{ $faq->answer }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="space-y-4">
                @foreach ($service->packages as $package)
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-white/10 dark:bg-zinc-900">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold capitalize text-zinc-900 dark:text-white">{{ $package->tier }}</h3>
                            <span class="text-xl font-bold text-zinc-900 dark:text-white">${{ number_format((float) $package->price, 0) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $package->description }}</p>
                        <p class="mt-3 text-xs font-medium text-zinc-400">{{ $package->delivery_days }} day delivery</p>
                    </div>
                @endforeach

                <a href="{{ $service->fiverr_url }}" target="_blank" rel="noopener" class="block rounded-full bg-zinc-900 px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                    Order on Fiverr
                </a>
                <a href="{{ route('contact.create') }}" class="block rounded-full border border-zinc-300 px-6 py-3 text-center text-sm font-semibold text-zinc-700 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-200">
                    Discuss Custom Scope
                </a>
            </aside>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="border-t border-zinc-200 bg-zinc-50 py-16 dark:border-white/10 dark:bg-white/[0.02]">
            <div class="mx-auto max-w-6xl px-6">
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Other Services</h2>
                <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($related as $item)
                        <x-service-card :service="$item" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.app>
