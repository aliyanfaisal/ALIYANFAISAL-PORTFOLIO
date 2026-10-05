@php
    use App\Support\Seo\Schema;
    use App\Support\Seo\Seo;

    $page = $posts->currentPage();
    $pageSuffix = $page > 1 ? " — Page {$page}" : '';
    $pageTitle = ($activeCategory ? $activeCategory->name.' Articles — Aliyan Faisal' : 'Blog — AI, LLM & Full-Stack Engineering — Aliyan Faisal').$pageSuffix;
    $metaDescription = $activeCategory
        ? ($activeCategory->description ?: "Articles about {$activeCategory->name} from Aliyan Faisal.")
        : config('seo.descriptions.blog');
    $canonicalUrl = Seo::canonical();
    $listingUrl = $activeCategory ? route('blog.category', $activeCategory) : route('blog.index');
    $crumbs = [['name' => 'Home', 'url' => route('home')], ['name' => 'Blog', 'url' => route('blog.index')]];
    if ($activeCategory) {
        $crumbs[] = ['name' => $activeCategory->name, 'url' => $listingUrl];
    }
    $breadcrumb = Schema::breadcrumbs($crumbs, $listingUrl);
    $graph = [
        Schema::personLite(),
        Schema::blog(),
        Schema::collectionPage($canonicalUrl, $pageTitle, $metaDescription, $breadcrumb['@id'], Schema::postList($posts->getCollection())),
        $breadcrumb,
    ];
    // Search results are thin, near-duplicate views of the listing: keep them out of the index.
    $robots = $search !== '' ? 'noindex, follow' : null;
@endphp
<x-layouts.app :title="$pageTitle" :description="$metaDescription" :canonical="$canonicalUrl" :robots="$robots" :graph="$graph">
    <section class="mx-auto max-w-4xl px-6 py-16 text-center">
        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400">Blog</p>
        <h1 class="mt-3 text-4xl font-bold text-zinc-900 dark:text-white">
            {{ $activeCategory ? $activeCategory->name : 'Articles & Insights' }}
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
            {{ $activeCategory?->description ?: 'Notes on LLM integration, AI automation, full-stack development and running it all on your own servers.' }}
        </p>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-20">
        <div class="flex flex-col gap-6 border-b border-zinc-200 pb-8 dark:border-white/10 md:flex-row md:items-center md:justify-between">
            <div class="flex min-w-0 flex-wrap gap-2">
                <a href="{{ route('blog.index', array_filter(['q' => $search])) }}"
                   class="rounded-full px-3 py-1.5 text-xs font-medium transition sm:px-4 sm:py-2 sm:text-sm {{ ! $activeCategory ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'border border-zinc-300 text-zinc-600 hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-300' }}">
                    All
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('blog.category', array_filter(['category' => $category->slug, 'q' => $search])) }}"
                       class="rounded-full px-3 py-1.5 text-xs font-medium transition sm:px-4 sm:py-2 sm:text-sm {{ $activeCategory?->id === $category->id ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'border border-zinc-300 text-zinc-600 hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-300' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            <form action="{{ $activeCategory ? route('blog.category', $activeCategory) : route('blog.index') }}" method="GET" class="relative w-full shrink-0 md:w-96">
                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search articles..."
                    class="w-full rounded-full border border-zinc-300 bg-white py-3.5 pl-12 pr-4 text-base text-zinc-700 placeholder:text-zinc-400 focus:border-indigo-400 focus:outline-none dark:border-white/15 dark:bg-zinc-900 dark:text-zinc-200"
                >
            </form>
        </div>

        @if ($posts->isEmpty())
            <p class="mt-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                @if ($search !== '' || $activeCategory)
                    No articles match your search — try a different keyword or category.
                @else
                    No articles published yet — check back soon.
                @endif
            </p>
        @else
            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($posts as $post)
                    <x-blog-card :post="$post" heading="h2" />
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>
