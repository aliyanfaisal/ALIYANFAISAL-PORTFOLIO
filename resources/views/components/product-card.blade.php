@props(['product'])

@php
    $isInvoice = $product['slug'] === 'invoiceinspect';
    $hasImage = $product['image'] && file_exists(public_path($product['image']));
@endphp

<a href="{{ route('products.show', $product['slug']) }}"
   class="group relative flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white transition hover:-translate-y-1 hover:border-indigo-400/50 hover:shadow-xl hover:shadow-indigo-500/10 dark:border-white/10 dark:bg-zinc-900">
    <div class="relative aspect-[16/9] overflow-hidden {{ $isInvoice ? 'bg-slate-950' : ($hasImage ? 'bg-violet-50' : 'bg-gradient-to-br from-amber-50 to-orange-100') }}">
        @if ($hasImage)
            <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] }} homepage preview" loading="lazy"
                 class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-[1.03]">
        @else
            {{-- mini kanban preview --}}
            <div class="absolute inset-0 grid grid-cols-3 gap-3 p-6 pt-12 transition duration-500 group-hover:scale-[1.03]" aria-hidden="true">
                @foreach ([['Pending', 'bg-amber-400', 3], ['Under Review', 'bg-sky-400', 2], ['Complete', 'bg-emerald-400', 3]] as [$col, $dot, $n])
                    <div class="rounded-xl bg-white/70 p-2 shadow-sm">
                        <p class="flex items-center gap-1 text-[9px] font-bold uppercase tracking-wide text-slate-500"><span class="size-1.5 rounded-full {{ $dot }}"></span>{{ $col }}</p>
                        @for ($i = 0; $i < $n; $i++)
                            <div class="mt-2 rounded-lg bg-white p-2 shadow-sm"><div class="h-1.5 w-4/5 rounded bg-slate-200"></div><div class="mt-1.5 h-1.5 w-1/2 rounded bg-slate-100"></div></div>
                        @endfor
                    </div>
                @endforeach
            </div>
        @endif
        <span class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-1 text-xs font-medium text-emerald-600 shadow backdrop-blur dark:bg-zinc-900/90 dark:text-emerald-400">
            <span class="size-1.5 rounded-full bg-emerald-500"></span>{{ $product['status'] }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-6">
        <span class="text-xs font-medium uppercase tracking-wide text-indigo-500 dark:text-indigo-400">{{ $product['category'] }}</span>
        <h3 class="mt-2 text-xl font-semibold text-zinc-900 dark:text-white">{{ $product['name'] }}</h3>
        <p class="mt-2 flex-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $product['summary'] }}</p>

        <div class="mt-4 flex flex-wrap gap-1.5">
            @foreach (array_slice($product['stack'], 0, 4) as $tech)
                <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs text-zinc-600 dark:bg-white/5 dark:text-zinc-300">{{ $tech }}</span>
            @endforeach
        </div>

        <span class="mt-5 inline-flex items-center gap-1 text-sm font-medium text-indigo-500 dark:text-indigo-400">
            {{ $product['url'] ? 'View product' : 'See details & get a demo' }}
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 transition group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </span>
    </div>
</a>
