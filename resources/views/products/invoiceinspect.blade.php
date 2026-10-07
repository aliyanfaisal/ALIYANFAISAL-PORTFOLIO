@php
    $graph = [
        \App\Support\Seo\Schema::personLite(),
        [
            '@type' => 'SoftwareApplication',
            '@id' => route('products.show', $product['slug']).'#product',
            'name' => $product['name'],
            'url' => $product['url'],
            'description' => $product['description'],
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
            'author' => \App\Support\Seo\Schema::personRef(),
        ],
        \App\Support\Seo\Schema::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Products', 'url' => route('products.index')],
            ['name' => $product['name'], 'url' => route('products.show', $product['slug'])],
        ], route('products.show', $product['slug'])),
    ];

    $steps = [
        ['Upload', 'Drop in a PDF invoice. No account needed.'],
        ['Read', 'Two independent readings: the PDF text layer (OCR for scans) and a vision AI reading the page images.'],
        ['Verify', 'Deterministic code recalculates every number. Exact, explainable, never a guess.'],
        ['Review', 'Each check ends as passed, warning, error or could not verify, with the evidence.'],
    ];

    $checks = [
        ['Line totals', 'Quantity × unit price'],
        ['Subtotal', 'Sum of all lines'],
        ['Tax', 'Rate × taxable amount'],
        ['Grand total', 'Lines − discount + shipping + tax'],
        ['Required details', 'Invoice number, dates, supplier, VAT ID'],
        ['Date logic', 'Due date before the issue date'],
    ];

    $principles = [
        ['Cross-checked reading', 'If both readings agree, confidence is high. If they disagree, the value is marked “could not verify” and no error is raised from it.'],
        ['Math is code, not AI', 'AI writes the plain-language explanation and a second look, and is always labeled “AI”. It can never hide an error on its own.'],
        ['Honest by design', 'Every value carries confidence and evidence. If something can’t be confirmed, it says so instead of guessing.'],
        ['Private by default', 'Guest uploads are processed in memory and never saved. No ads, no third-party analytics.'],
    ];
@endphp
<x-layouts.app :full-bleed="true" :title="'InvoiceInspect — Free Invoice Checker & Validator | Aliyan Faisal'" :description="\Illuminate\Support\Str::limit($product['description'], 157)" :graph="$graph">
    <div class="relative overflow-hidden pt-20 bg-slate-950 text-slate-300" style="font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif">
        {{-- background --}}
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -top-40 left-1/2 h-[560px] w-[900px] -translate-x-1/2 rounded-full bg-indigo-600/25 blur-[140px]"></div>
            <div class="bg-grid absolute inset-0 opacity-60"></div>
        </div>

        {{-- Hero --}}
        <section class="relative mx-auto max-w-6xl px-6 pb-16 pt-12 text-center">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-slate-400 hover:text-indigo-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                All products
            </a>

            <div class="mt-6 flex items-center justify-center gap-3">
                <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-800 shadow-lg shadow-indigo-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="10.5" cy="10.5" r="6"/><path d="M15 15l5 5M8.5 10.5l1.5 1.5 3-3"/></svg>
                </span>
                <span class="text-2xl font-bold tracking-tight text-white">Invoice<span class="bg-gradient-to-r from-indigo-400 to-indigo-300 bg-clip-text text-transparent">Inspect</span></span>
            </div>

            <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-extrabold tracking-tight text-white sm:text-6xl">
                Find invoice errors <span class="bg-gradient-to-r from-indigo-400 to-violet-400 bg-clip-text text-transparent">before you pay.</span>
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-400">
                A free online invoice checker and validator. It recalculates the numbers on any PDF invoice and shows every mismatch with evidence. No signup required.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ $product['url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:bg-indigo-400">
                    Check an invoice free
                    <x-icon name="arrow-up-right" class="size-4" />
                </a>
                <a href="#how" class="rounded-full border border-white/10 px-6 py-3 text-sm font-semibold text-slate-200 transition hover:border-indigo-400/50 hover:text-white">How it works</a>
            </div>

            <div class="mx-auto mt-6 flex max-w-xl flex-wrap items-center justify-center gap-x-4 gap-y-1 text-xs text-slate-500">
                <span class="text-indigo-300">Free · No signup required</span>
                <span>3 checks a day</span><span>Files up to 5 MB</span><span>Up to 5 pages</span>
            </div>

            {{-- screenshot in browser frame --}}
            <div class="relative mx-auto mt-14 max-w-5xl">
                <div class="absolute -inset-4 rounded-[2rem] bg-gradient-to-b from-indigo-500/30 to-transparent blur-2xl"></div>
                <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-slate-900 shadow-2xl shadow-black/60">
                    <div class="flex items-center gap-2 border-b border-white/10 bg-slate-900 px-4 py-3">
                        <span class="size-2.5 rounded-full bg-rose-400/70"></span><span class="size-2.5 rounded-full bg-amber-400/70"></span><span class="size-2.5 rounded-full bg-emerald-400/70"></span>
                        <span class="mx-auto rounded-md bg-slate-800 px-10 py-1 text-xs text-slate-400">{{ $product['domain'] }}</span>
                    </div>
                    <img src="{{ asset($product['image']) }}" alt="InvoiceInspect homepage: Invoice Validator Online, No Signup Required" width="1920" height="1085" class="w-full">
                </div>
            </div>
        </section>

        {{-- Result mock --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-20">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-indigo-400">See the evidence</span>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-white">Every finding shows its working.</h2>
                    <p class="mt-4 text-slate-400">No vague “something looks off”. Each finding states the expected amount, the printed amount, the difference and the page it came from, followed by a plain-language AI explanation with a next step.</p>
                    <ul class="mt-6 space-y-3 text-sm">
                        <li class="flex gap-3"><span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-400">✓</span>One printed mistake gives one finding, not a pile of noise.</li>
                        <li class="flex gap-3"><span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-400">✓</span>Rules only fail on high-confidence values.</li>
                        <li class="flex gap-3"><span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-emerald-500/15 text-emerald-400">✓</span>Low-confidence values go to “Please confirm” so you stay in control.</li>
                    </ul>
                </div>

                {{-- finding card --}}
                <div class="rounded-2xl border border-white/10 bg-slate-900/80 p-6 shadow-xl backdrop-blur">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-white">Invoice #INV-2041</span>
                        <span class="rounded-full bg-rose-500/15 px-3 py-1 text-xs font-semibold text-rose-300">1 error</span>
                    </div>
                    <div class="mt-5 divide-y divide-white/5 text-sm">
                        <div class="flex items-center justify-between py-3"><span class="flex items-center gap-2"><span class="size-2 rounded-full bg-emerald-400"></span>Line totals</span><span class="text-emerald-300">Passed</span></div>
                        <div class="flex items-center justify-between py-3"><span class="flex items-center gap-2"><span class="size-2 rounded-full bg-emerald-400"></span>Subtotal</span><span class="text-emerald-300">Passed</span></div>
                        <div class="flex items-center justify-between py-3"><span class="flex items-center gap-2"><span class="size-2 rounded-full bg-amber-400"></span>VAT ID</span><span class="text-amber-300">Warning</span></div>
                        <div class="flex items-center justify-between py-3"><span class="flex items-center gap-2"><span class="size-2 rounded-full bg-rose-400"></span>Grand total</span><span class="text-rose-300">Error</span></div>
                    </div>
                    <div class="mt-4 rounded-xl border border-rose-500/25 bg-rose-500/10 p-4">
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div><p class="text-[11px] uppercase tracking-wide text-slate-500">Expected</p><p class="mt-1 font-mono text-base font-semibold text-white">1,180.00</p></div>
                            <div><p class="text-[11px] uppercase tracking-wide text-slate-500">Printed</p><p class="mt-1 font-mono text-base font-semibold text-rose-300">1,810.00</p></div>
                            <div><p class="text-[11px] uppercase tracking-wide text-slate-500">Difference</p><p class="mt-1 font-mono text-base font-semibold text-rose-300">630.00</p></div>
                        </div>
                        <p class="mt-3 border-t border-white/10 pt-3 text-xs text-slate-400"><span class="mr-1 rounded bg-indigo-500/20 px-1.5 py-0.5 font-semibold text-indigo-300">AI</span>Page 1: the lines, tax and shipping add up to 1,180.00, but 1,810.00 is printed. The digits look transposed. Ask the supplier for a corrected invoice.</p>
                    </div>
                    <p class="mt-3 text-center text-[11px] text-slate-600">Illustrative example</p>
                </div>
            </div>
        </section>

        {{-- How it works --}}
        <section id="how" class="reveal relative border-t border-white/5 bg-white/[0.02] py-20">
            <div class="mx-auto max-w-6xl px-6">
                <div class="text-center">
                    <span class="text-xs font-semibold uppercase tracking-widest text-indigo-400">How a check works</span>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-white">From PDF to verdict in seconds</h2>
                </div>
                <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($steps as $i => [$name, $text])
                        <div class="relative rounded-2xl border border-white/10 bg-slate-900/60 p-6">
                            <span class="font-mono text-sm text-indigo-400">0{{ $i + 1 }}</span>
                            <h3 class="mt-3 text-lg font-semibold text-white">{{ $name }}</h3>
                            <p class="mt-2 text-sm text-slate-400">{{ $text }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- What it verifies --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-20">
            <div class="text-center">
                <span class="text-xs font-semibold uppercase tracking-widest text-indigo-400">What it verifies</span>
                <h2 class="mt-3 text-3xl font-bold tracking-tight text-white">The numbers, recalculated</h2>
            </div>
            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($checks as [$name, $text])
                    <div class="flex items-start gap-4 rounded-2xl border border-white/10 bg-slate-900/60 p-5 transition hover:border-indigo-400/40">
                        <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-indigo-500/15 text-indigo-300"><x-icon name="badge-check" class="size-5" /></span>
                        <div>
                            <h3 class="font-semibold text-white">{{ $name }}</h3>
                            <p class="mt-1 text-sm text-slate-400">{{ $text }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Principles --}}
        <section class="reveal relative border-t border-white/5 bg-white/[0.02] py-20">
            <div class="mx-auto max-w-6xl px-6">
                <div class="max-w-2xl">
                    <span class="text-xs font-semibold uppercase tracking-widest text-indigo-400">Design principles</span>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-white">Calm, precise and honest about its limits.</h2>
                    <p class="mt-4 text-slate-400">I built InvoiceInspect so that “could not verify” is a feature. It checks arithmetic and consistency; it doesn’t prove an invoice is genuine and isn’t accounting, tax or legal advice.</p>
                </div>
                <div class="mt-10 grid gap-5 md:grid-cols-2">
                    @foreach ($principles as [$name, $text])
                        <div class="rounded-2xl border border-white/10 bg-gradient-to-br from-slate-900 to-slate-950 p-6">
                            <h3 class="text-lg font-semibold text-white">{{ $name }}</h3>
                            <p class="mt-2 text-sm text-slate-400">{{ $text }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Built with --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-20">
            <div class="grid gap-10 lg:grid-cols-3">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-widest text-indigo-400">Under the hood</span>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight text-white">Built with</h2>
                </div>
                <div class="lg:col-span-2">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($product['stack'] as $tech)
                            <span class="rounded-full border border-white/10 bg-slate-900 px-4 py-2 text-sm text-slate-300">{{ $tech }}</span>
                        @endforeach
                    </div>
                    <dl class="mt-8 grid gap-6 sm:grid-cols-3">
                        <div><dt class="text-xs uppercase tracking-wide text-slate-500">Field accuracy</dt><dd class="mt-1 text-3xl font-bold text-white">~99.5%</dd><p class="mt-1 text-xs text-slate-500">Rules extractor, 300 synthetic invoices</p></div>
                        <div><dt class="text-xs uppercase tracking-wide text-slate-500">Recall</dt><dd class="mt-1 text-3xl font-bold text-white">93%</dd><p class="mt-1 text-xs text-slate-500">Error detection, end to end</p></div>
                        <div><dt class="text-xs uppercase tracking-wide text-slate-500">Precision</dt><dd class="mt-1 text-3xl font-bold text-white">93%</dd><p class="mt-1 text-xs text-slate-500">Error detection, end to end</p></div>
                    </dl>
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="relative px-6 pb-24">
            <div class="mx-auto max-w-4xl overflow-hidden rounded-3xl border border-indigo-400/20 bg-gradient-to-br from-indigo-600 to-indigo-900 p-10 text-center shadow-2xl shadow-indigo-900/40 sm:p-14">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Check your next invoice before you pay it.</h2>
                <p class="mx-auto mt-3 max-w-xl text-indigo-100/80">Free. No signup required. Your guest upload is never saved.</p>
                <a href="{{ $product['url'] }}" target="_blank" rel="noopener" class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-7 py-3 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50">
                    Open InvoiceInspect
                    <x-icon name="arrow-up-right" class="size-4" />
                </a>
            </div>

            @foreach ($others as $other)
                <p class="mt-10 text-center text-sm text-slate-500">
                    Also by me: <a href="{{ route('products.show', $other['slug']) }}" class="font-medium text-indigo-300 hover:underline">{{ $other['name'] }}</a> &mdash; {{ $other['tagline'] }}
                </p>
            @endforeach
        </section>
    </div>
</x-layouts.app>
