@php
    $title = 'Products — Aliyan Faisal';
    $description = 'Products built by Aliyan Faisal: InvoiceInspect, a free invoice checker and validator, and Cuelara, an AI toolkit and prompt book.';
    $graph = [
        \App\Support\Seo\Schema::personLite(),
        \App\Support\Seo\Schema::collectionPage(route('products.index'), 'Products', $description, route('products.index').'#breadcrumb', \App\Support\Seo\Schema::itemList(collect($products)->map(fn ($p) => ['url' => route('products.show', $p['slug']), 'name' => $p['name']])->values()->all())),
        \App\Support\Seo\Schema::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Products', 'url' => route('products.index')],
        ], route('products.index')),
    ];
@endphp
<x-layouts.app :title="$title" :description="$description" :graph="$graph">
    <section class="mx-auto max-w-6xl px-6 py-12">
        <span class="text-xs font-medium uppercase tracking-wide text-indigo-500 dark:text-indigo-400">Built by me</span>
        <h1 class="mt-2 text-3xl font-bold text-zinc-900 dark:text-white">My Products</h1>
        <p class="mt-3 max-w-2xl text-zinc-500 dark:text-zinc-400">Beyond client work, I design, build and run my own products end to end.</p>

        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
            <x-product-coming-soon />
        </div>
    </section>
</x-layouts.app>
