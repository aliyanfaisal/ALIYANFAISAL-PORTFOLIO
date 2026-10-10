{{-- Notemind in use. Sample data; mirrors what the plugin prints in Claude Code. --}}
<style>
    .nm-cursor::after { content: '▍'; animation: nm-blink 1s steps(2) infinite; opacity: .7 }
    @keyframes nm-blink { 50% { opacity: 0 } }
    @media (prefers-reduced-motion: reduce) { .nm-cursor::after { animation: none } }
</style>

<section id="in-use" class="scroll-mt-24">
    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">See it in use</h2>
    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Five steps, from typing a note to closing it. The data below is a sample.</p>

    <nav class="mt-4 flex flex-wrap gap-2 text-sm" aria-label="Walkthrough steps">
        @foreach ([['add', '1 · Add'], ['alert', '2 · Startup alert'], ['today', '3 · Today'], ['list', '4 · List'], ['done', '5 · Done']] as [$id, $label])
            <a href="#nm-{{ $id }}" class="rounded-full border border-zinc-200 bg-white px-3 py-1.5 transition hover:border-indigo-400 hover:text-indigo-500 dark:border-white/10 dark:bg-zinc-900">{{ $label }}</a>
        @endforeach
    </nav>

    <div class="mt-8 space-y-12">
        {{-- 1 · Add --}}
        <div id="nm-add" class="scroll-mt-24">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">1 · Adding a note</h3>
            <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">Write the date however you like. Claude reads it and saves the exact time.</p>

            <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-white/10 dark:bg-zinc-900">
                <div class="flex items-center gap-2 border-b border-zinc-200 px-4 py-2.5 text-xs text-zinc-500 dark:border-white/10">
                    <span class="size-2.5 rounded-full bg-red-400"></span><span class="size-2.5 rounded-full bg-amber-400"></span><span class="size-2.5 rounded-full bg-green-400"></span>
                    <span class="ml-2">Claude Code · local session</span>
                </div>
                <div class="space-y-5 p-5 text-[15px]">
                    <div class="flex justify-end">
                        <div class="max-w-[85%] rounded-2xl rounded-br-md bg-zinc-100 px-4 py-2.5 font-mono text-sm dark:bg-zinc-800">
                            <span class="text-orange-600 dark:text-orange-400">/note-add</span> send the invoice tomorrow at 3pm
                        </div>
                    </div>
                    <div class="rounded-lg border border-zinc-200 px-3 py-2 font-mono text-xs text-zinc-500 dark:border-white/10">
                        ⏺ Bash(<span class="text-zinc-700 dark:text-zinc-300">notescos add --due 2026-10-11T15:00 …</span>)
                    </div>
                    <div>
                        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 dark:border-green-900 dark:bg-green-950/40">
                            <p class="font-medium">✅ Saved note <b>#3</b></p>
                            <p class="mt-1">send the invoice</p>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">📅 Sun 11 Oct, 15:00 · task</p>
                        </div>
                        <p class="mt-3 text-sm italic text-zinc-500">Assumed “tomorrow” means Sun 11 Oct. Editing notes isn’t available yet, so run the command again with the date written out if that’s wrong.</p>
                    </div>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-2 font-mono text-xs">
                @foreach (['dentist in the next 2 hours', 'end of next month', 'the 3rd Friday of May'] as $example)
                    <span class="rounded-md border border-zinc-200 bg-white px-2.5 py-1 dark:border-white/10 dark:bg-zinc-900">{{ $example }}</span>
                @endforeach
                <span class="rounded-md border border-zinc-200 bg-white px-2.5 py-1 dark:border-white/10 dark:bg-zinc-900">waiting on invoice from Sara <i class="not-italic text-zinc-400">(no date)</i></span>
            </div>
        </div>

        {{-- 2 · Startup alert --}}
        <div id="nm-alert" class="scroll-mt-24">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">2 · The startup alert</h3>
            <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">Shown when a session starts, but only if something is overdue or due within 2 days.</p>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <p class="mb-2 text-xs uppercase tracking-wider text-zinc-500">Terminal / VS Code</p>
                    <div class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-950 font-mono text-[13px] leading-relaxed text-zinc-200 shadow-sm">
                        <div class="flex items-center gap-2 border-b border-zinc-800 px-4 py-2.5 text-xs text-zinc-500">
                            <span class="size-2.5 rounded-full bg-red-400"></span><span class="size-2.5 rounded-full bg-amber-400"></span><span class="size-2.5 rounded-full bg-green-400"></span>
                            <span class="ml-2">~/project — claude</span>
                        </div>
                        <div class="space-y-3 p-4">
                            <p class="text-zinc-500">SessionStart hook</p>
                            <div class="rounded-lg border border-orange-500/40 bg-orange-500/10 px-3 py-2.5">
                                <p>📌 Notes: <span class="text-red-400">2 overdue</span></p>
                                <p class="pl-3"><span class="text-red-400">!</span> #1 I have a meeting in next 2 hours <span class="text-zinc-500">(overdue 1d)</span></p>
                                <p class="pl-3"><span class="text-red-400">!</span> #2 remind me tomorrow at 12am <span class="text-zinc-500">(overdue today)</span></p>
                                <p class="pl-3 text-zinc-500">/notemind:note-today for details</p>
                            </div>
                            <p class="rounded-lg border border-zinc-700 px-3 py-2 text-zinc-400"><span class="text-orange-400">&gt;</span> <span class="nm-cursor"></span></p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="mb-2 text-xs uppercase tracking-wider text-zinc-500">Claude app (Code tab)</p>
                    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white text-[15px] shadow-sm dark:border-white/10 dark:bg-zinc-900">
                        <div class="flex items-center justify-between border-b border-zinc-200 px-4 py-2.5 text-xs text-zinc-500 dark:border-white/10">
                            <span>Code · my-project</span><span class="rounded-full bg-zinc-100 px-2 py-0.5 dark:bg-zinc-800">Local</span>
                        </div>
                        <div class="space-y-4 p-5">
                            <div class="flex justify-end">
                                <div class="rounded-2xl rounded-br-md bg-zinc-100 px-4 py-2.5 dark:bg-zinc-800">Let’s fix the login bug</div>
                            </div>
                            <div class="space-y-3">
                                <div class="rounded-xl border-l-4 border-orange-500 bg-orange-50 px-4 py-3 dark:bg-orange-950/30">
                                    <p class="font-semibold">📌 You have 2 overdue notes</p>
                                    <ul class="mt-1.5 space-y-1 text-sm">
                                        <li><b class="text-red-600 dark:text-red-400">#1</b> I have a meeting in next 2 hours <span class="text-zinc-500">· overdue 1d</span></li>
                                        <li><b class="text-red-600 dark:text-red-400">#2</b> remind me tomorrow at 12am <span class="text-zinc-500">· overdue today</span></li>
                                    </ul>
                                    <p class="mt-2 text-xs text-zinc-500">Run <span class="font-mono">/note-today</span> for details.</p>
                                </div>
                                <p>Sure, let’s take a look at the login flow. Which error are you seeing?</p>
                            </div>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-zinc-500">In the app, Claude opens its first reply with the alert.</p>
                </div>
            </div>
        </div>

        {{-- 3 · Today --}}
        <div id="nm-today" class="scroll-mt-24">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">3 · What needs attention</h3>
            <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400"><span class="font-mono">/note-today</span> groups notes by urgency.</p>

            <div class="space-y-6 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-zinc-900">
                <h4 class="text-xl font-semibold text-zinc-900 dark:text-white">📌 Today <span class="font-normal text-zinc-400">· Sat 10 Oct</span></h4>

                <div>
                    <h5 class="mb-2 font-semibold text-red-600 dark:text-red-400">🔴 Overdue (2)</h5>
                    <div class="overflow-x-auto"><table class="w-full min-w-[420px] table-fixed text-sm">
                        <colgroup><col class="w-10"><col><col class="w-40 sm:w-48"></colgroup>
                        <thead><tr class="border-b border-zinc-200 text-left text-zinc-500 dark:border-white/10"><th class="py-2 pr-3 text-right">#</th><th class="py-2 pr-3">Note</th><th class="py-2">Was due</th></tr></thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                            <tr><td class="py-2 pr-3 text-right text-zinc-500">1</td><td class="py-2 pr-3">I have a meeting in next 2 hours</td><td class="py-2 text-red-600 dark:text-red-400">overdue 1d</td></tr>
                            <tr><td class="py-2 pr-3 text-right text-zinc-500">2</td><td class="py-2 pr-3">remind me tomorrow at 12am</td><td class="py-2 text-red-600 dark:text-red-400">overdue today</td></tr>
                        </tbody>
                    </table></div>
                </div>

                @foreach ([
                    ['🟡 Due today (1)', 'text-amber-600 dark:text-amber-400', [['4', 'dentist in the next 2 hours', '12:30']]],
                    ['🔵 Coming up (1)', 'text-sky-600 dark:text-sky-400', [['3', 'send the invoice', 'Sun 11 Oct, 15:00']]],
                    ['⚪ No date (1)', 'text-zinc-500', [['5', 'waiting on invoice from Sara', '']]],
                ] as [$group, $color, $rows])
                    <div>
                        <h5 class="mb-2 font-semibold {{ $color }}">{{ $group }}</h5>
                        <div class="overflow-x-auto"><table class="w-full min-w-[420px] table-fixed text-sm"><colgroup><col class="w-10"><col><col class="w-40 sm:w-48"></colgroup><tbody>
                            @foreach ($rows as [$n, $text, $when])
                                <tr><td class="py-2 pr-3 text-right text-zinc-500">{{ $n }}</td><td class="py-2 pr-3">{{ $text }}</td><td class="py-2">{{ $when }}</td></tr>
                            @endforeach
                        </tbody></table></div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 4 · List --}}
        <div id="nm-list" class="scroll-mt-24">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">4 · Browsing all notes</h3>
            <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400"><span class="font-mono">/note-list</span> shows every open note. Add finished ones or filter by project or type.</p>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-zinc-900">
                <h4 class="mb-3 text-xl font-semibold text-zinc-900 dark:text-white">🗒️ Notes <span class="font-normal text-zinc-400">· 5 open</span></h4>
                <div class="overflow-x-auto"><table class="w-full min-w-[520px] text-sm">
                    <thead><tr class="border-b border-zinc-200 text-left text-zinc-500 dark:border-white/10">
                        <th class="w-10 py-2 pr-3 text-right">#</th><th class="py-2 pr-3">Note</th><th class="py-2 pr-3">Due</th><th class="py-2">Status</th></tr></thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-white/5">
                        @foreach ([
                            ['1', 'I have a meeting in next 2 hours', 'Fri 9 Oct, 05:53', '🔴 overdue 1d', 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300'],
                            ['2', 'remind me tomorrow at 12am', 'Sat 10 Oct, 00:00', '🔴 overdue today', 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300'],
                            ['4', 'dentist in the next 2 hours', 'Sat 10 Oct, 12:30', '🟡 today', 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300'],
                            ['3', 'send the invoice', 'Sun 11 Oct, 15:00', '🔵 in 1d', 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300'],
                            ['5', 'waiting on invoice from Sara', '—', '⚪ no date', 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400'],
                        ] as [$n, $text, $due, $status, $badge])
                            <tr>
                                <td class="py-2 pr-3 text-right text-zinc-500">{{ $n }}</td>
                                <td class="py-2 pr-3">{{ $text }}</td>
                                <td class="py-2 pr-3 {{ $due === '—' ? 'text-zinc-400' : '' }}">{{ $due }}</td>
                                <td class="py-2"><span class="rounded-full px-2 py-0.5 text-xs {{ $badge }}">{{ $status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            </div>
        </div>

        {{-- 5 · Done --}}
        <div id="nm-done" class="scroll-mt-24">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">5 · Marking done</h3>
            <div class="mt-3 space-y-4 overflow-hidden rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-zinc-900">
                <div class="flex justify-end">
                    <div class="rounded-2xl rounded-br-md bg-zinc-100 px-4 py-2.5 font-mono text-sm dark:bg-zinc-800"><span class="text-orange-600 dark:text-orange-400">/note-done</span> 1</div>
                </div>
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 dark:border-green-900 dark:bg-green-950/40">
                    ✅ Done: <s class="text-zinc-500">#1 I have a meeting in next 2 hours</s>
                </div>
            </div>
        </div>
    </div>

    <p class="mt-6 text-xs text-zinc-500">Sample data. All notes stay in <span class="font-mono">~/.notescos/notes.db</span> on your computer.</p>
</section>
