<?php

/*
| Open-source projects I build. Add a new entry here and it appears in the homepage section,
| the /open-source index, its own /open-source/{slug} details page and the sitemap.
| Optional keys (commands, how_it_works, data_model, roadmap, ...) are simply skipped when absent.
*/
return [
    'notemind' => [
        'name' => 'Notemind',
        'slug' => 'notemind',
        'repo_url' => 'https://github.com/aliyanfaisal/notemind',
        'category' => 'Claude Code Plugin · Productivity',
        'tagline' => 'Notes and follow-ups for Claude Code. Write them in plain words, get reminded when they are due.',
        'summary' => 'Type a note like "call Sara tomorrow at 3pm". Notemind works out the date, saves it on your computer, and shows what is overdue or due soon each time a session starts. It also tracks money and follow-ups, and answers questions about your notes.',
        'description' => 'Notemind is an open-source Claude Code plugin by Aliyan Faisal. Write notes in plain words, it works out the date, stores them locally in SQLite, and shows what is overdue or due soon at the start of every session. It tracks who owes you, what you owe and what you are waiting on, and you can ask questions about your notes. No account, no network calls of its own.',
        'status' => 'Open source · MIT',
        'version' => '0.10.0',
        'license' => 'MIT',
        'requires' => 'Python 3.9+',
        'language' => 'Python',
        'stack' => ['Python', 'SQLite', 'Claude Code plugin', 'Claude skills', 'Hooks'],

        'highlights' => [
            ['Plain-language capture', 'Dates, times, amounts and people are read from your sentence, for example "Ali owes me 15k PKR, due Friday".'],
            ['Startup briefing', 'Overdue and upcoming notes appear when a session starts, with a quiet, normal or loud banner level.'],
            ['Money and follow-ups', 'Track who owes you, what you owe, and what you are waiting on.'],
            ['Ask your notes', 'note-ask answers questions such as "what is pending for Ali?" and cites note numbers.'],
            ['Local by default', 'One file on your machine (~/.notescos/notes.db), no account, no network calls of its own. Claude sees note text only when it has to help, and sensitive data is masked first.'],
        ],

        'install' => [
            'claude plugin marketplace add aliyanfaisal/notemind',
            'claude plugin install notemind@notemind',
        ],
        'try' => '/notemind:note-add call Sara tomorrow at 3pm',

        'commands' => [
            ['/notemind:note-add', 'Add a note in plain words. The date, amount and person are read from your sentence.', true],
            ['/notemind:note-today', 'Show what is overdue, due today and coming up in the next 7 days.', false],
            ['/notemind:note-top', 'Show the notes that matter most right now, with the reason for each.', false],
            ['/notemind:note-check', 'Show everything needing attention, including follow-ups waiting too long.', false],
            ['/notemind:note-list', 'Browse notes, including finished ones, or filter by project, type or date.', false],
            ['/notemind:note-board', 'Show notes on a visual board.', false],
            ['/notemind:note-ask', 'Ask a question about your notes; answers cite note numbers.', false],
            ['/notemind:note-edit', 'Change a note, in flags or plain words.', false],
            ['/notemind:note-snooze', 'Hide a note until later.', false],
            ['/notemind:note-done', 'Mark a note as done.', true],
            ['/notemind:note-forget', 'Delete notes permanently (projects and all are previewed first).', false],
            ['/notemind:note-sync', 'Find commitments in past sessions and save the ones you approve. Off until enabled.', false],
            ['/notemind:note-settings', 'Set how much the startup banner shows.', false],
            ['/notemind:note-tour', 'A two-minute guided tour.', false],
            ['/notemind:note-help', 'Show the command list.', false],
        ],
        'commands_note' => 'Commands marked "You only" cannot be triggered by Claude. See the guide for what Claude can run when you ask.',

        'how_it_works' => [
            ['Date interpretation', 'The note-add skill asks Claude to work out the due date from your sentence. The current date and time go into the prompt, and the result is saved as YYYY-MM-DD or YYYY-MM-DDTHH:MM. A rule-based parser lives in core/notescos/parse.py.'],
            ['Storage', 'SQLite with versioned, append-only migrations (store.py). The database is not removed when you uninstall the plugin.'],
            ['The brief', 'brief.py groups notes into overdue, due today, upcoming and no date. hooks.py runs it from one SessionStart hook.'],
            ['Quiet by design', 'The hook stays silent when nothing is overdue, due today or due within 2 days, on compact events, and for notes set to silent. Errors are logged and swallowed so it can never break a session. A run takes about 85 ms.'],
            ['Least privilege', 'Each skill is limited to running the bundled tool, and the two that change data are user-invocable only.'],
        ],
        'architecture' => [
            ['.claude-plugin/', 'plugin.json and marketplace.json: the plugin manifest and marketplace.'],
            ['skills/', 'One skill per command.'],
            ['core/notescos/', 'About 900 lines: store.py, brief.py, hooks.py, cli.py and parse.py.'],
            ['scripts/notescos', 'A small launcher for the CLI (add, list, today, done, hook).'],
            ['release.sh', 'Validates the plugin and builds dist/notemind-v<version>.zip.'],
        ],

        'data_model' => [
            ['Text and status', 'text, type and status. Types: task, followup, waiting_on, money_owed, money_due, decision, idea, reference. Status: open, done or snoozed.'],
            ['Timing', 'due_at, remind_at and snoozed_until.'],
            ['Importance', 'priority (0–3) and alert_level (normal, urgent or silent).'],
            ['Context', 'person, amount, currency, project, branch and tags.'],
            ['Origin', 'source, source_ref and confirmed, plus created, updated and done timestamps.'],
        ],

            ],
];
