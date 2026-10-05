@props(['service', 'bestSeller' => false, 'heading' => 'h3'])

<a href="{{ route('services.show', $service) }}" class="group relative flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:-translate-y-1 hover:border-indigo-400/50 hover:shadow-lg hover:shadow-indigo-500/5 dark:border-white/10 dark:bg-zinc-900">
    @if ($bestSeller)
        <span class="absolute left-4 top-4 z-10 inline-flex items-center gap-1 rounded-full bg-amber-400 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-amber-950 shadow-sm">
            <x-icon name="star" class="size-3" /> Best Seller
        </span>
    @endif

    @if ($service->image_url)
        <div class="aspect-video w-full overflow-hidden bg-zinc-100 dark:bg-white/5">
            <img src="{{ $service->image_url }}" alt="{{ $service->title }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        </div>
    @endif

    <div class="flex flex-1 flex-col p-6">
        @if ($service->category)
            <span class="text-xs font-medium uppercase tracking-wide text-indigo-500 dark:text-indigo-400">{{ $service->category }}</span>
        @endif

        <{{ $heading }} class="mt-2 font-semibold capitalize text-zinc-900 dark:text-white">{{ $service->title }}</{{ $heading }}>

        <div class="mt-4 flex items-center gap-1 text-sm text-amber-500">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.37-2.448a1 1 0 00-1.176 0l-3.37 2.448c-.783.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.363-1.118l-3.37-2.448c-.782-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.285-3.958z"/></svg>
            <span class="font-medium text-zinc-700 dark:text-zinc-200">{{ number_format((float) $service->rating, 1) }}</span>
            <span class="text-zinc-400">({{ $service->rating_count }})</span>
        </div>

        <div class="mt-auto flex items-center justify-between pt-4">
            <span class="text-sm text-zinc-500 dark:text-zinc-400">Starting at</span>
            <span class="text-lg font-bold text-zinc-900 dark:text-white">${{ number_format((float) $service->price_from, 0) }}</span>
        </div>
    </div>
</a>
