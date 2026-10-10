@php
    $homeTitle = config('seo.default_title');
    $homeDescription = config('seo.default_description');
    $graph = [
        \App\Support\Seo\Schema::person(),
        \App\Support\Seo\Schema::website(),
        \App\Support\Seo\Schema::profilePage(url('/'), $homeTitle, $homeDescription),
    ];
@endphp
<x-layouts.app :title="$homeTitle" :description="$homeDescription" :graph="$graph">
    {{-- Hero --}}
    <section class="glow-gradient relative overflow-hidden pt-20">
        <div class="bg-grid absolute inset-0 -z-10"></div>

        {{-- ambient floating tech icons --}}
        <div class="pointer-events-none absolute inset-0 -z-10 hidden overflow-hidden text-indigo-400 dark:text-indigo-300 sm:block" aria-hidden="true">
            <x-icon name="code" class="animate-float absolute left-[6%] top-[18%] size-8 opacity-40" style="animation-delay: 0.2s" />
            <x-icon name="cube" class="animate-float absolute left-[16%] top-[68%] size-7 opacity-35" style="animation-delay: 2.1s" />
            <x-icon name="cpu-chip" class="animate-float absolute left-[38%] top-[8%] size-6 opacity-45" style="animation-delay: 1.3s" />
            <x-icon name="circle-stack" class="animate-float absolute left-[30%] top-[85%] size-7 opacity-35" style="animation-delay: 3.4s" />
            <x-icon name="cloud" class="animate-float absolute right-[8%] top-[10%] size-7 opacity-40" style="animation-delay: 0.8s" />
            <x-icon name="link" class="animate-float absolute right-[4%] top-[55%] size-6 opacity-35" style="animation-delay: 2.6s" />
            <x-icon name="shopping-cart" class="animate-float absolute right-[20%] top-[88%] size-7 opacity-40" style="animation-delay: 1.8s" />
            <x-icon name="sparkles" class="animate-float absolute left-[48%] top-[45%] size-6 opacity-35" style="animation-delay: 4s" />
        </div>

        <div class="mx-auto grid max-w-6xl gap-12 px-6 pb-16 pt-6 md:grid-cols-2 md:items-center md:pb-24 md:pt-10">
            <div>
                <p class="inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-500 dark:text-indigo-300">
                    Islamabad, Pakistan
                </p>

                <h1 class="mt-6 text-4xl font-bold tracking-tight text-zinc-900 sm:text-5xl dark:text-white">
                    <span class="block text-base font-semibold tracking-normal text-indigo-500 sm:text-lg dark:text-indigo-300">Software Engineer &amp; AI Developer</span>
                    Building
                    <span
                        x-data="{
                            words: ['AI-Powered', 'LLM-Integrated', 'RAG-Driven', 'Agentic', 'Full-Stack', 'Production-Ready'],
                            i: 0,
                            visible: true,
                        }"
                        x-init="setInterval(() => { visible = false; setTimeout(() => { i = (i + 1) % words.length; visible = true }, 250) }, 2400)"
                        class="text-gradient block transition-all duration-300"
                        :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-1'"
                        x-text="words[i]"
                    >AI-Powered</span>
                    web products.
                </h1>

                <p class="mt-6 max-w-xl text-lg text-zinc-600 dark:text-zinc-400">
                    I'm Aliyan Faisal, a software engineer and AI developer. I build LLM integrations, RAG pipelines, chatbots and workflow automations with OpenAI, Claude and Gemini — plus the full-stack apps (Laravel, Node.js, React) and servers they run on. 5+ years shipping production systems.
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('products.index') }}" class="rounded-full bg-zinc-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                        View My Work
                    </a>
                    <a href="{{ route('contact.create') }}" class="rounded-full border border-zinc-300 px-6 py-3 text-sm font-semibold text-zinc-700 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/15 dark:text-zinc-200">
                        Get in Touch
                    </a>
                </div>

                <dl class="mt-12 grid grid-cols-3 gap-6 border-t border-zinc-200 pt-8 dark:border-white/10">
                    <div>
                        <dt class="text-2xl font-bold text-zinc-900 dark:text-white">5+</dt>
                        <dd class="text-xs text-zinc-500 dark:text-zinc-400">Years Experience</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-bold text-zinc-900 dark:text-white">{{ count(config('products')) }}</dt>
                        <dd class="text-xs text-zinc-500 dark:text-zinc-400">Products Owned &amp; many more built as a freelancer</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-bold text-zinc-900 dark:text-white">3.80 CGPA</dt>
                        <dd class="text-xs text-zinc-500 dark:text-zinc-400">BSc IT, Karakoram International University</dd>
                    </div>
                </dl>
            </div>

            <div class="relative mx-auto w-full max-w-sm order-first md:order-none">
                {{-- spotlight glow behind subject --}}
                <div class="absolute left-1/2 top-1/2 -z-10 h-[110%] w-[85%] -translate-x-1/2 -translate-y-1/2 animate-pulse-slow rounded-full bg-gradient-to-br from-indigo-500/50 via-violet-500/40 to-cyan-400/40 blur-3xl"></div>

                {{-- orbiting ring accents --}}
                <div class="absolute inset-0 -z-10 rounded-full border border-indigo-400/20"></div>
                <div class="absolute -inset-6 -z-10 rounded-full border border-dashed border-violet-400/15"></div>
                <div class="absolute -inset-12 -z-10 rounded-full border border-cyan-400/10"></div>

                {{-- ambient particles --}}
                <span class="absolute -left-10 top-4 -z-10 size-2 animate-float rounded-full bg-indigo-400/60 blur-[1px]" style="animation-delay: 0.5s"></span>
                <span class="absolute -right-8 top-20 -z-10 size-1.5 animate-float rounded-full bg-cyan-400/60 blur-[1px]" style="animation-delay: 1.8s"></span>
                <span class="absolute left-2 -top-6 -z-10 size-1.5 animate-float rounded-full bg-violet-400/60 blur-[1px]" style="animation-delay: 3s"></span>
                <span class="absolute -right-4 bottom-24 -z-10 size-2 animate-float rounded-full bg-indigo-400/50 blur-[1px]" style="animation-delay: 0.9s"></span>
                <span class="absolute -left-6 bottom-4 -z-10 size-1.5 animate-float rounded-full bg-cyan-400/50 blur-[1px]" style="animation-delay: 2.4s"></span>

                <img src="{{ asset('images/aliyan_navy_suit_cutout.png') }}" alt="Aliyan Faisal" class="fade-top relative z-10 mx-auto h-auto w-[85%] -scale-x-100 rounded-b-[50%] drop-shadow-[0_20px_40px_rgba(79,70,229,0.35)] sm:w-full">

                {{-- grounding shadow --}}
                <div class="mx-auto -mt-6 h-6 w-2/3 rounded-full bg-zinc-900/20 blur-xl dark:bg-black/40"></div>

                <div class="glass-card animate-float absolute -left-2 top-2 z-20 hidden w-40 lg:-left-14 lg:block" style="animation-delay: 0s">
                    <div class="grid size-8 place-items-center rounded-lg bg-gradient-to-br from-indigo-500/15 via-violet-500/15 to-cyan-400/15 text-indigo-500 dark:text-indigo-300"><x-icon name="cpu-chip" class="size-4" /></div>
                    <p class="mt-1.5 text-xs font-semibold text-zinc-800 dark:text-zinc-100">AI Integrations</p>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">OpenAI · Claude · Gemini</p>
                </div>

                
                <div class="glass-card animate-float absolute -right-14 top-1/4 z-20 hidden w-40 lg:block" style="animation-delay: 1.2s">
                    <div class="grid size-8 place-items-center rounded-lg bg-gradient-to-br from-violet-500/15 via-fuchsia-500/15 to-transparent text-violet-500 dark:text-violet-300"><x-icon name="rocket" class="size-4" /></div>
                    <p class="mt-1.5 text-xs font-semibold text-zinc-800 dark:text-zinc-100">Products I Own</p>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">InvoiceInspect · Cuelara · ManaJet</p>
                </div>

                <div class="glass-card animate-float absolute -left-2 bottom-10 z-20 w-44 lg:-left-16" style="animation-delay: 2.4s">
                    <div class="grid size-8 place-items-center rounded-lg bg-gradient-to-br from-cyan-500/15 via-indigo-500/15 to-transparent text-cyan-500 dark:text-cyan-300"><x-icon name="bolt" class="size-4" /></div>
                    <p class="mt-1.5 text-xs font-semibold text-zinc-800 dark:text-zinc-100">Full-Stack Engineering</p>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Laravel · Node · React · DevOps</p>
                </div>
            </div>
        </div>

        {{-- Tech marquee --}}
        <div class="border-t border-zinc-200 py-6 dark:border-white/10">
            <div class="overflow-hidden">
                <div class="animate-marquee flex w-max items-center gap-12 text-sm font-medium text-zinc-400 dark:text-zinc-500">
                    @php
                        $stack = ['OpenAI', 'Claude', 'Gemini', 'Groq', 'RAG Systems', 'LangChain', 'Vector DBs', 'LLM Agents', 'n8n / Automation', 'Laravel', 'Node.js', 'React / Next.js', 'WordPress / WooCommerce', 'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'NumPy / Pandas', 'REST APIs', 'Linux / Nginx', 'Docker'];
                        $stack = array_merge($stack, $stack);
                    @endphp
                    @foreach ($stack as $tech)
                        <span class="flex items-center gap-2 whitespace-nowrap">
                            <span class="size-1.5 rounded-full bg-indigo-400/70"></span>
                            {{ $tech }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- My Products --}}
    <section class="reveal border-t border-zinc-200 py-20 dark:border-white/10">
        <div class="mx-auto max-w-6xl px-6">
            <div class="flex items-end justify-between">
                <div>
                    <span class="text-xs font-medium uppercase tracking-wide text-indigo-500 dark:text-indigo-400">Owned &amp; operated by me</span>
                    <h2 class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">Products I Built &amp; Own</h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-medium text-indigo-500 hover:underline dark:text-indigo-400">View all &rarr;</a>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach (config('products') as $product)
                    <x-product-card :product="$product" />
                @endforeach
                <x-product-coming-soon />
            </div>
        </div>
    </section>

    {{-- Open source --}}
    <section class="reveal border-t border-zinc-200 py-20 dark:border-white/10">
        <div class="mx-auto max-w-6xl px-6">
            <div class="flex items-end justify-between">
                <div>
                    <span class="text-xs font-medium uppercase tracking-wide text-indigo-500 dark:text-indigo-400">Free to use, free to read</span>
                    <h2 class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">Open Source I Build</h2>
                </div>
                <a href="{{ route('open-source.index') }}" class="text-sm font-medium text-indigo-500 hover:underline dark:text-indigo-400">View all &rarr;</a>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach (config('opensource') as $project)
                    <x-open-source-card :project="$project" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Recent GitHub repos --}}
    @if (! empty($featuredRepos))
        <section class="reveal pb-20">
            <div class="mx-auto max-w-6xl px-6">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">My Recent GitHub Repos</h2>
                        @if ($githubProfile)
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $githubProfile['public_repos'] }} public repos &middot; pulled live from GitHub</p>
                        @endif
                    </div>
                    <a href="https://github.com/aliyanfaisal" target="_blank" rel="noopener" class="text-sm font-medium text-indigo-500 hover:underline dark:text-indigo-400">All on GitHub &rarr;</a>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($featuredRepos as $repo)
                        <x-repo-card :repo="$repo" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Skills --}}
    @php
        $skillIcons = [
            'Backend' => 'cog',
            'CMS' => 'globe',
            'AI & Automation' => 'cpu-chip',
            'Frontend' => 'swatch',
            'DevOps' => 'server',
            'Security & Performance' => 'shield-check',
        ];
        $categoryColors = [
            'Backend' => ['grad' => 'from-indigo-500 to-blue-500', 'tint' => 'from-indigo-500/10 via-blue-500/5 to-transparent', 'border' => 'hover:border-indigo-400/40', 'glow' => 'hover:shadow-indigo-500/10'],
            'CMS' => ['grad' => 'from-violet-500 to-fuchsia-500', 'tint' => 'from-violet-500/10 via-fuchsia-500/5 to-transparent', 'border' => 'hover:border-violet-400/40', 'glow' => 'hover:shadow-violet-500/10'],
            'AI & Automation' => ['grad' => 'from-cyan-500 to-indigo-500', 'tint' => 'from-cyan-500/10 via-indigo-500/5 to-transparent', 'border' => 'hover:border-cyan-400/40', 'glow' => 'hover:shadow-cyan-500/10'],
            'Frontend' => ['grad' => 'from-rose-500 to-orange-400', 'tint' => 'from-rose-500/10 via-orange-400/5 to-transparent', 'border' => 'hover:border-rose-400/40', 'glow' => 'hover:shadow-rose-500/10'],
            'DevOps' => ['grad' => 'from-emerald-500 to-teal-500', 'tint' => 'from-emerald-500/10 via-teal-500/5 to-transparent', 'border' => 'hover:border-emerald-400/40', 'glow' => 'hover:shadow-emerald-500/10'],
            'Security & Performance' => ['grad' => 'from-amber-500 to-red-500', 'tint' => 'from-amber-500/10 via-red-500/5 to-transparent', 'border' => 'hover:border-amber-400/40', 'glow' => 'hover:shadow-amber-500/10'],
        ];
        $defaultColor = ['grad' => 'from-indigo-500 to-cyan-400', 'tint' => 'from-indigo-500/10 via-cyan-400/5 to-transparent', 'border' => 'hover:border-indigo-400/40', 'glow' => 'hover:shadow-indigo-500/10'];
    @endphp
    @if ($skills->isNotEmpty())
        <section class="mx-auto max-w-6xl px-6 py-16">
            <p class="reveal text-center text-sm font-semibold uppercase tracking-widest text-zinc-400">Core Expertise</p>
            <h2 class="reveal mt-2 text-center text-2xl font-bold text-zinc-900 dark:text-white">The stack behind every build</h2>
            <p class="reveal mx-auto mt-2 max-w-md text-center text-sm text-zinc-500 dark:text-zinc-400">Hover a card to see proficiency.</p>

            <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach ($skills as $skill)
                    @php $c = $categoryColors[$skill->category] ?? $defaultColor; @endphp
                    <div
                        class="group reveal relative overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-50 p-4 text-center transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl dark:border-white/10 dark:bg-white/5 {{ $c['border'] }} {{ $c['glow'] }}"
                        style="transition-delay: {{ ($loop->index % 4) * 70 }}ms"
                    >
                        <div class="absolute inset-0 -z-10 bg-gradient-to-br {{ $c['tint'] }} opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>

                        <div class="mx-auto grid size-11 place-items-center rounded-xl bg-gradient-to-br {{ $c['grad'] }} text-white shadow-md transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6">
                            <x-icon :name="$skillIcons[$skill->category] ?? 'wrench'" class="size-5" />
                        </div>
                        <p class="mt-3 text-sm font-semibold text-zinc-800 dark:text-zinc-100">{{ $skill->name }}</p>
                        <p class="text-[10px] uppercase tracking-wide text-zinc-400">{{ $skill->category }}</p>

                        <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-white/10">
                            <div class="skill-fill h-full rounded-full bg-gradient-to-r {{ $c['grad'] }}" style="width: 0%" data-width="{{ $skill->proficiency }}"></div>
                        </div>
                        <p class="mt-1.5 text-[11px] font-medium text-zinc-500 opacity-0 transition-opacity duration-300 group-hover:opacity-100 dark:text-zinc-400">
                            {{ $skill->proficiency }}% proficiency
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- AI-Powered Development --}}
        <section class="reveal glow-gradient relative overflow-hidden border-t border-zinc-200 py-20 dark:border-white/10">
            <div class="bg-grid absolute inset-0 -z-10 opacity-60"></div>
            <div class="mx-auto max-w-6xl px-6">
                <div class="grid gap-10 md:grid-cols-2 md:items-center">
                    <div>
                        <p class="inline-flex items-center gap-1.5 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-500 dark:text-indigo-300">
                            <x-icon name="cpu-chip" class="size-3.5" /> AI &amp; Automation
                        </p>
                        <h2 class="mt-4 text-3xl font-bold text-zinc-900 dark:text-white">AI is the thread through my work.</h2>
                        <p class="mt-4 text-zinc-600 dark:text-zinc-400">
                            I integrate LLM APIs — OpenAI, Claude, Gemini and Groq — into real products: RAG systems and chatbots over private data (ingestion, embeddings, vector search, cited answers), support and order-handling automation, content pipelines, and agentic workflows, deployed and monitored on servers I configure myself.
                        </p>
                        <a href="{{ route('blog.index') }}" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold text-indigo-500 hover:underline dark:text-indigo-400">
                            Read my AI articles &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        @php
                            $aiCards = [
                                ['icon' => 'circle-stack', 'title' => 'RAG Systems', 'desc' => 'Ingestion, chunking, embeddings and vector search for grounded, cited answers.'],
                                ['icon' => 'chat-bubble', 'title' => 'AI Chatbots', 'desc' => 'Support assistants grounded in product docs, FAQs and policies.'],
                                ['icon' => 'cog', 'title' => 'Workflow Automation', 'desc' => 'Automating orders, emails and repetitive store tasks.'],
                                ['icon' => 'link', 'title' => 'API Integrations', 'desc' => 'Connecting apps to OpenAI, Claude, Gemini & Groq.'],
                                ['icon' => 'chart-bar', 'title' => 'AI SEO & Content', 'desc' => 'Generating product copy, meta tags & keyword content.'],
                                ['icon' => 'light-bulb', 'title' => 'Custom AI Products', 'desc' => 'Designing and building AI-powered tools and products from the ground up.'],
                            ];
                        @endphp
                        @foreach ($aiCards as $card)
                            <div class="group rounded-2xl border border-zinc-200 bg-white p-5 transition hover:-translate-y-1 hover:border-indigo-400/40 hover:shadow-lg hover:shadow-indigo-500/10 dark:border-white/10 dark:bg-zinc-900 {{ $card['span'] ?? false ? 'col-span-2' : '' }}">
                                <div class="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-indigo-500/15 via-violet-500/15 to-cyan-400/15 text-indigo-500 transition group-hover:scale-110 dark:text-indigo-300">
                                    <x-icon :name="$card['icon']" class="size-5" />
                                </div>
                                <p class="mt-3 text-sm font-semibold text-zinc-900 dark:text-white">{{ $card['title'] }}</p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $card['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

    {{-- Skills & Expertise (full breakdown) --}}
    @if ($allSkills->isNotEmpty())
        <section class="reveal border-t border-zinc-200 bg-zinc-50 py-20 dark:border-white/10 dark:bg-white/[0.02]">
            <div class="mx-auto max-w-6xl px-6">
                <p class="text-center text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400">Full Skill Breakdown</p>
                <h2 class="mt-2 text-center text-2xl font-bold text-zinc-900 dark:text-white">Skills &amp; Expertise</h2>

                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($allSkills->groupBy('category') as $category => $categorySkills)
                        @php $c = $categoryColors[$category] ?? $defaultColor; @endphp
                        <div class="reveal rounded-2xl border border-zinc-200 bg-white p-6 transition hover:-translate-y-1 hover:shadow-lg dark:border-white/10 dark:bg-zinc-900 {{ $c['border'] }} {{ $c['glow'] }}">
                            <div class="flex items-center gap-2.5">
                                <div class="grid size-8 place-items-center rounded-lg bg-gradient-to-br {{ $c['grad'] }} text-white shadow-sm">
                                    <x-icon :name="$skillIcons[$category] ?? 'wrench'" class="size-4" />
                                </div>
                                <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $category }}</h3>
                            </div>

                            <div class="mt-5 space-y-4">
                                @foreach ($categorySkills as $skill)
                                    <div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-medium text-zinc-700 dark:text-zinc-200">{{ $skill->name }}</span>
                                            <span class="text-zinc-400">{{ $skill->proficiency }}%</span>
                                        </div>
                                        <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-white/10">
                                            <div class="skill-fill h-full rounded-full bg-gradient-to-r {{ $c['grad'] }}" style="width: 0%" data-width="{{ $skill->proficiency }}"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Connect --}}
    <section class="reveal border-t border-zinc-200 py-20 dark:border-white/10">
        <div class="mx-auto max-w-6xl px-6">
            <p class="text-center text-sm font-semibold uppercase tracking-widest text-indigo-500 dark:text-indigo-400">Where to Find Me</p>
            <h2 class="mt-2 text-center text-2xl font-bold text-zinc-900 dark:text-white">Let's Connect</h2>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $platforms = [
                        [
                            'name' => 'LinkedIn',
                            'subtitle' => 'Connect professionally',
                            'url' => 'https://www.linkedin.com/in/aliyan-faisal-5162261b7/',
                            'badge' => 'bg-[#0a66c2]',
                            'icon' => '<path d="M6.94 8.5H4.56V19h2.38V8.5zM5.75 4.75a1.38 1.38 0 100 2.76 1.38 1.38 0 000-2.76zM19.44 19h-2.37v-5.4c0-1.29-.46-2.16-1.6-2.16-.88 0-1.4.59-1.63 1.16-.08.2-.1.49-.1.77V19H11.4s.03-9.6 0-10.5h2.37v1.49a2.35 2.35 0 012.13-1.18c1.56 0 2.73 1.02 2.73 3.2V19z" fill="white"/>',
                        ],
                        [
                            'name' => 'GitHub',
                            'subtitle' => 'Code & open source',
                            'url' => 'https://github.com/aliyanfaisal',
                            'badge' => 'bg-zinc-800',
                            'icon' => '<path d="M12 .5a11.5 11.5 0 00-3.64 22.41c.58.11.79-.25.79-.56v-2c-3.2.7-3.88-1.37-3.88-1.37-.52-1.33-1.28-1.69-1.28-1.69-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.19 1.76 1.19 1.03 1.76 2.69 1.25 3.35.96.1-.75.4-1.25.73-1.54-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.28 1.18-3.09-.12-.29-.51-1.46.11-3.05 0 0 .97-.31 3.17 1.18a11 11 0 015.78 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.18 1.83 1.18 3.09 0 4.42-2.69 5.39-5.26 5.68.41.36.78 1.06.78 2.14v3.17c0 .31.21.68.8.56A11.5 11.5 0 0012 .5z" fill="white"/>',
                        ],
                        [
                            'name' => 'Email',
                            'subtitle' => 'contact@aliyanfaisal.com',
                            'url' => 'mailto:contact@aliyanfaisal.com',
                            'badge' => 'bg-indigo-500',
                            'icon' => '<path d="M4 4h16v16H4z" stroke="white" stroke-width="2" fill="none" stroke-linejoin="round"/><path d="M22 6l-10 7L2 6" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
                        ],
                    ];
                @endphp
                @foreach ($platforms as $platform)
                    <a
                        href="{{ $platform['url'] }}"
                        @if (!str_starts_with($platform['url'], 'mailto:')) target="_blank" rel="noopener" @endif
                        class="group flex items-center gap-4 rounded-2xl border border-zinc-200 bg-white p-5 transition hover:-translate-y-1 hover:border-indigo-400/40 hover:shadow-lg hover:shadow-indigo-500/5 dark:border-white/10 dark:bg-zinc-900"
                    >
                        <span class="grid size-11 shrink-0 place-items-center rounded-xl {{ $platform['badge'] }} shadow-sm transition group-hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="size-5">{!! $platform['icon'] !!}</svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-zinc-900 dark:text-white">{{ $platform['name'] }}</span>
                            <span class="block truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $platform['subtitle'] }}</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-auto size-4 shrink-0 text-zinc-300 transition group-hover:translate-x-0.5 group-hover:text-indigo-400 dark:text-zinc-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M7 7h10v10"/></svg>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Latest Blog Posts --}}
    @if ($latestPosts->isNotEmpty())
        <section class="reveal border-t border-zinc-200 py-20 dark:border-white/10">
            <div class="mx-auto max-w-6xl px-6">
                <div class="flex items-end justify-between">
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">From the Blog</h2>
                    <a href="{{ route('blog.index') }}" class="text-sm font-medium text-indigo-500 hover:underline dark:text-indigo-400">View all &rarr;</a>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($latestPosts as $post)
                        <x-blog-card :post="$post" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="reveal py-20">
        <div class="glow-gradient relative mx-auto max-w-4xl overflow-hidden rounded-3xl border border-indigo-500/20 px-6 py-16 text-center">
            <div class="bg-grid absolute inset-0 -z-10 opacity-50"></div>
            <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">Get in touch</h2>
            <p class="mt-4 text-zinc-600 dark:text-zinc-400">Questions about my work, ideas to collaborate on, or just want to say hi — I'd love to hear from you.</p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('contact.create') }}" class="rounded-full bg-zinc-900 px-8 py-3 text-sm font-semibold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-zinc-900 dark:hover:bg-indigo-400">
                    Contact Me
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
