@php
    $graph = [
        \App\Support\Seo\Schema::personLite(),
        [
            '@type' => 'SoftwareApplication',
            '@id' => route('products.show', $product['slug']).'#product',
            'name' => $product['name'],
            'url' => route('products.show', $product['slug']),
            'description' => $product['description'],
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'author' => \App\Support\Seo\Schema::personRef(),
        ],
        \App\Support\Seo\Schema::breadcrumbs([
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Products', 'url' => route('products.index')],
            ['name' => $product['name'], 'url' => route('products.show', $product['slug'])],
        ], route('products.show', $product['slug'])),
    ];

    $roles = [
        ['Project Manager', 'Sets up projects, builds teams, plans tasks, tracks progress and controls settings.', 'bg-orange-100 text-orange-700'],
        ['Team Members', 'See only their own tasks, update progress, submit work for review and chat with colleagues.', 'bg-sky-100 text-sky-700'],
        ['Clients', 'Follow their own project’s progress and raise tickets. No account needed.', 'bg-emerald-100 text-emerald-700'],
    ];

    $features = [
        ['Projects', 'Name, category, budget, cover image and team. Live progress from completed vs. pending tasks, plus a project board.'],
        ['Tasks', 'Ordered steps with description, deadline, priority, a responsible member and file attachments.'],
        ['Teams', 'Group developers, pick a team lead, add or remove members. Members only see their teams’ projects.'],
        ['Tickets', 'Bugs and change requests raised by team members or clients against a project or a specific task.'],
        ['Roles & permissions', 'Web, Mobile, Frontend, Backend, iOS, Android, Media Marketer. The manager decides what each role can do.'],
        ['Chat & notice board', 'One-to-one chat with file sharing, company-wide announcements and in-app notifications.'],
        ['Dashboard & analytics', 'Totals, income from completed project budgets, best-performing team, and charts by priority and month.'],
        ['WhatsApp alerts', 'Optional alerts when a project is assigned, a task is given or a ticket is raised.'],
    ];

    $shots = collect([
        ['images/products/manajet-dashboard.webp', 'Dashboard', 'Totals, income, best team and charts at a glance.'],
        ['images/products/manajet-projects.webp', 'All projects', 'Every project with its team, category, live progress and status.'],
        ['images/products/manajet-add-project.webp', 'Add a project', 'Name, category, budget, team and cover image, with a live preview card.'],
        ['images/products/manajet-categories.webp', 'Project categories', 'Organize projects into categories and sub-categories.'],
    ])->filter(fn ($shot) => file_exists(public_path($shot[0])))->values();

    $aiTasks = [
        ['Requirements & wireframes', 'High', 3, 'bg-rose-100 text-rose-700'],
        ['Theme setup & page layouts', 'High', 4, 'bg-rose-100 text-rose-700'],
        ['Portfolio gallery with Elementor', 'Medium', 3, 'bg-amber-100 text-amber-700'],
        ['SEO basics & speed pass', 'Low', 2, 'bg-emerald-100 text-emerald-700'],
    ];
@endphp
<x-layouts.app :full-bleed="true" :title="'ManaJet — Project Management for IT Teams | Aliyan Faisal'" :description="\Illuminate\Support\Str::limit($product['description'], 157)" :graph="$graph">
    <div class="relative overflow-hidden bg-[#fbf7f0] pt-20 text-slate-600">
        <div class="pointer-events-none absolute -right-40 top-0 size-[560px] rounded-full bg-orange-200/50 blur-[120px]" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -left-40 top-[520px] size-[480px] rounded-full bg-sky-200/50 blur-[120px]" aria-hidden="true"></div>

        {{-- Hero --}}
        <section class="relative mx-auto max-w-6xl px-6 pb-20 pt-12">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-orange-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                All products
            </a>

            <div class="mt-8 grid items-center gap-14 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <div class="flex items-center gap-3">
                        <span class="grid size-11 place-items-center rounded-xl bg-slate-900 text-white shadow-lg shadow-slate-900/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-orange-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 18V6l8 8 8-8v12"/></svg>
                        </span>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-900">Mana<span class="text-orange-500">Jet</span></span>
                    </div>

                    <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                        Project management that <span class="text-orange-500">software teams</span> actually finish in.
                    </h1>
                    <p class="mt-5 max-w-xl text-lg text-slate-600">
                        Projects, teams, tasks, client requests and chat in one place, with an optional AI assistant that drafts your task plan. <em class="font-semibold not-italic text-slate-800">Mobilize, Organize, and Excel.</em>
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="#demo" class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-900/25 transition hover:-translate-y-0.5 hover:bg-orange-600">
                            Get a demo
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
                        </a>
                        <a href="#features" class="rounded-full border border-slate-300 bg-white px-7 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-orange-400">See features</a>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">Built for small and mid-sized software companies. Runs on standard web hosting.</p>
                </div>

                @if ($shots->isNotEmpty())
                    <div class="relative lg:col-span-7">
                        <div class="absolute -inset-3 rotate-1 rounded-[2rem] bg-gradient-to-br from-orange-200 to-amber-100"></div>
                        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10">
                            <img src="{{ asset($shots[0][0]) }}" alt="ManaJet dashboard with project, ticket, income and team totals" class="aspect-[4/3] w-full object-fill" fetchpriority="high">
                        </div>
                    </div>
                @else
                {{-- kanban mock --}}
                <div class="relative lg:col-span-7">
                    <div class="absolute -inset-3 rotate-1 rounded-[2rem] bg-gradient-to-br from-orange-200 to-amber-100"></div>
                    <div class="relative rounded-3xl border border-slate-200 bg-white p-5 shadow-2xl shadow-slate-900/10">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Project</p>
                                <p class="font-bold text-slate-900">Portfolio Website</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-slate-500">62%</span>
                                <span class="h-2 w-24 overflow-hidden rounded-full bg-slate-100"><span class="block h-full w-[62%] rounded-full bg-gradient-to-r from-orange-400 to-orange-500"></span></span>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-3 text-xs">
                            @php
                                $board = [
                                    ['Pending', 'bg-amber-400', [['Gallery page', 'High'], ['Contact form', 'Medium']]],
                                    ['Under Review', 'bg-sky-400', [['Homepage layout', 'High']]],
                                    ['Complete', 'bg-emerald-400', [['Wireframes', 'High'], ['Theme setup', 'Medium'], ['Brand assets', 'Low']]],
                                ];
                            @endphp
                            @foreach ($board as [$col, $dot, $cards])
                                <div class="rounded-2xl bg-slate-50 p-2.5">
                                    <p class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wide text-slate-500"><span class="size-2 rounded-full {{ $dot }}"></span>{{ $col }}</p>
                                    @foreach ($cards as [$name, $prio])
                                        <div class="mt-2 rounded-xl border border-slate-100 bg-white p-2.5 shadow-sm">
                                            <p class="font-semibold leading-snug text-slate-800">{{ $name }}</p>
                                            <p class="mt-1.5 text-[10px] font-medium {{ $prio === 'High' ? 'text-rose-500' : ($prio === 'Medium' ? 'text-amber-500' : 'text-emerald-500') }}">● {{ $prio }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <p class="mt-3 text-center text-[11px] text-slate-400">Illustrative board</p>
                </div>
                @endif
            </div>
        </section>

        {{-- Screenshots --}}
        @if ($shots->count() > 1)
            <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
                <div class="text-center">
                    <span class="text-xs font-bold uppercase tracking-widest text-orange-600">Inside ManaJet</span>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">The manager’s view</h2>
                </div>
                <div class="mt-10 grid gap-8 md:grid-cols-2">
                    @foreach ($shots->skip(1) as [$src, $label, $caption])
                        <figure>
                            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-900/5">
                                <img src="{{ asset($src) }}" alt="ManaJet {{ strtolower($label) }} screen" loading="lazy" class="aspect-[4/3] w-full object-fill">
                            </div>
                            <figcaption class="mt-3 text-sm"><strong class="text-slate-800">{{ $label }}.</strong> <span class="text-slate-500">{{ $caption }}</span></figcaption>
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Roles --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-orange-600">Who it’s for</span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">One tool, three points of view</h2>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($roles as [$role, $text, $chip])
                    <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        <span class="inline-block rounded-full px-3 py-1 text-xs font-bold {{ $chip }}">{{ $role }}</span>
                        <p class="mt-4 text-slate-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Workflow --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="rounded-[2rem] bg-slate-900 p-8 text-white sm:p-12">
                <span class="text-xs font-bold uppercase tracking-widest text-orange-400">Task workflow</span>
                <h2 class="mt-3 max-w-2xl text-3xl font-extrabold tracking-tight">Nothing is “done” until the manager says so.</h2>
                <div class="mt-10 flex flex-col items-stretch gap-4 md:flex-row md:items-center">
                    @foreach ([['Pending', 'The task is assigned to a team member.', 'border-amber-400/40 text-amber-300'], ['Under Review', 'The member submits finished work for review.', 'border-sky-400/40 text-sky-300'], ['Complete', 'The manager approves and progress updates.', 'border-emerald-400/40 text-emerald-300']] as $i => [$stage, $text, $c])
                        <div class="flex-1 rounded-2xl border bg-white/5 p-5 {{ $c }}">
                            <p class="text-sm font-bold">{{ $stage }}</p>
                            <p class="mt-1 text-sm text-slate-300">{{ $text }}</p>
                        </div>
                        @if ($i < 2)<span class="hidden text-2xl text-slate-500 md:block">→</span>@endif
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section id="features" class="reveal relative mx-auto max-w-6xl scroll-mt-24 px-6 py-16">
            <div class="text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-orange-600">Everything in one place</span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Features</h2>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($features as $i => [$name, $text])
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-orange-300 hover:shadow-lg hover:shadow-orange-500/10">
                        <span class="font-mono text-xs font-bold text-orange-500">0{{ $i + 1 }}</span>
                        <h3 class="mt-2 font-bold text-slate-900">{{ $name }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-500">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- AI planning --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div>
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">Optional AI assistant</span>
                    <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900">A first draft of your project plan in seconds.</h2>
                    <p class="mt-4 text-slate-600">Click <strong class="text-slate-800">Auto Generate Tasks</strong>, add a hint or two, and ManaJet suggests an Agile-style task list with a description, priority and estimated days for each task.</p>
                    <ul class="mt-6 space-y-3 text-sm text-slate-600">
                        <li class="flex gap-3"><span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-orange-100 text-xs font-bold text-orange-600">1</span><span><strong class="text-slate-800">You stay in control.</strong> Nothing is added automatically. Click “Edit and Add”, adjust the prefilled form and save.</span></li>
                        <li class="flex gap-3"><span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-orange-100 text-xs font-bold text-orange-600">2</span><span><strong class="text-slate-800">Deadlines calculated for you</strong> from the estimated days.</span></li>
                        <li class="flex gap-3"><span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-orange-100 text-xs font-bold text-orange-600">3</span><span><strong class="text-slate-800">Your account, your cost.</strong> It uses your company’s own OpenAI account and can be switched off in Settings.</span></li>
                        <li class="flex gap-3"><span class="mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-orange-100 text-xs font-bold text-orange-600">4</span><span>Past suggestions are kept for each project.</span></li>
                    </ul>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5">
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-slate-900">Suggested tasks</p>
                        <span class="rounded-full bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white">✨ Auto Generate Tasks</span>
                    </div>
                    <div class="mt-2 rounded-xl bg-slate-50 px-3 py-2 text-xs text-slate-400">Hints: WordPress, portfolio website, Elementor</div>
                    <div class="mt-4 space-y-3">
                        @foreach ($aiTasks as [$task, $prio, $days, $chip])
                            <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $task }}</p>
                                    <p class="mt-1 flex items-center gap-2 text-xs text-slate-500"><span class="rounded-full px-2 py-0.5 font-semibold {{ $chip }}">{{ $prio }}</span>{{ $days }} days</p>
                                </div>
                                <span class="shrink-0 rounded-full border border-orange-300 px-3 py-1.5 text-xs font-semibold text-orange-600">Edit and Add</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-3 text-center text-[11px] text-slate-400">Illustrative example</p>
                </div>
            </div>
        </section>

        {{-- Client portal --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 py-16">
            <div class="grid items-center gap-12 rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm sm:p-12 lg:grid-cols-2">
                <div class="order-2 lg:order-1">
                    <div class="rounded-2xl bg-slate-50 p-5">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Client portal</p>
                        <div class="mt-3 flex items-center gap-2 rounded-xl border border-dashed border-orange-300 bg-white px-4 py-3 font-mono text-sm text-slate-700">
                            <span class="text-orange-500">🔑</span> proj-7f3a-91c2-d84e
                        </div>
                        <div class="mt-4 flex items-center justify-between text-sm"><span class="font-semibold text-slate-800">Portfolio Website</span><span class="font-semibold text-orange-600">62% done</span></div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200"><div class="h-full w-[62%] rounded-full bg-gradient-to-r from-orange-400 to-orange-500"></div></div>
                        <div class="mt-4 flex gap-2 text-xs font-semibold"><span class="rounded-full bg-slate-900 px-3 py-1.5 text-white">Read-only view</span><span class="rounded-full border border-slate-300 px-3 py-1.5 text-slate-600">Raise a ticket</span></div>
                    </div>
                </div>
                <div class="order-1 lg:order-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-orange-600">For your clients</span>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">Share progress with a single key.</h2>
                    <p class="mt-4 text-slate-600">Every project gets a private secret key. Give it to your client and they get a read-only view of that project’s progress and can raise tickets, with no account to create and no access to any other project.</p>
                </div>
            </div>
        </section>

        {{-- Demo form --}}
        <section id="demo" class="reveal relative mx-auto max-w-6xl scroll-mt-24 px-6 py-16">
            <div class="overflow-hidden rounded-[2rem] bg-slate-900 shadow-2xl shadow-slate-900/20">
                <div class="grid lg:grid-cols-5">
                    <div class="p-8 text-white sm:p-12 lg:col-span-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-orange-400">Get a demo</span>
                        <h2 class="mt-3 text-3xl font-extrabold tracking-tight">See ManaJet with your own projects in mind.</h2>
                        <p class="mt-4 text-slate-300">Tell me a little about your team and I’ll reply by email to set up a walkthrough. ManaJet is sold to companies, so there’s no public sign-up.</p>
                        <ul class="mt-6 space-y-2 text-sm text-slate-300">
                            <li>✓ A live walkthrough of the manager, team and client views</li>
                            <li>✓ Answers on setup, hosting and customization</li>
                            <li>✓ No obligation</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('products.demo') }}" class="space-y-5 bg-white p-8 sm:p-12 lg:col-span-3">
                        @csrf

                        @if (session('demo_status'))
                            <div class="rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('demo_status') }}</div>
                        @endif

                        {{-- honeypot --}}
                        <div class="absolute -left-[9999px]" aria-hidden="true">
                            <label for="website">Website</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="demo-name" class="block text-sm font-semibold text-slate-700">Name</label>
                                <input type="text" name="name" id="demo-name" value="{{ old('name') }}" required maxlength="100" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="demo-email" class="block text-sm font-semibold text-slate-700">Work email</label>
                                <input type="email" name="email" id="demo-email" value="{{ old('email') }}" required maxlength="150" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="demo-company" class="block text-sm font-semibold text-slate-700">Company</label>
                                <input type="text" name="company" id="demo-company" value="{{ old('company') }}" maxlength="150" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                                @error('company') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="demo-team" class="block text-sm font-semibold text-slate-700">Team size</label>
                                <select name="team_size" id="demo-team" class="mt-1.5 w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/30">
                                    <option value="">Select…</option>
                                    @foreach (['1-5', '6-15', '16-50', '50+'] as $size)
                                        <option value="{{ $size }}" @selected(old('team_size') === $size)>{{ $size }} people</option>
                                    @endforeach
                                </select>
                                @error('team_size') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="demo-message" class="block text-sm font-semibold text-slate-700">What would you like to manage with ManaJet? <span class="font-normal text-slate-400">(optional)</span></label>
                            <textarea name="message" id="demo-message" rows="4" maxlength="3000" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500/30">{{ old('message') }}</textarea>
                            @error('message') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="w-full rounded-full bg-orange-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/30 transition hover:bg-orange-600">Request my demo</button>
                    </form>
                </div>
            </div>
        </section>

        {{-- Tech + repo --}}
        <section class="reveal relative mx-auto max-w-6xl px-6 pb-24 pt-8">
            <div class="flex flex-col items-start justify-between gap-6 border-t border-slate-200 pt-10 md:flex-row md:items-center">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400">Built with</h2>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($product['stack'] as $tech)
                            <span class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-slate-200">{{ $tech }}</span>
                        @endforeach
                    </div>
                </div>
                <a href="{{ $product['repo_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-orange-600">
                    <x-brand-icon name="github" class="size-4" />
                    ManaJet showcase on GitHub
                </a>
            </div>

            @foreach ($others as $other)
                <p class="mt-8 text-sm text-slate-500">
                    Also by me: <a href="{{ route('products.show', $other['slug']) }}" class="font-semibold text-orange-600 hover:underline">{{ $other['name'] }}</a> &mdash; {{ $other['tagline'] }}
                </p>
            @endforeach
        </section>
    </div>
</x-layouts.app>
