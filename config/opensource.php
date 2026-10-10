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
        'tagline' => 'Notes in plain words, right inside Claude Code.',
        'summary' => 'Type a note like "call Sara tomorrow at 3pm". Notemind works out the date, saves it locally, and shows what is overdue each time a session starts.',
        'description' => 'Notemind is an open-source Claude Code plugin by Aliyan Faisal. Write notes in plain words, it works out the due date, stores them locally in SQLite, and shows what is overdue or due soon at the start of every session. No account, no network calls.',
        'status' => 'Open source · MIT',
        'version' => '0.7.1',
        'license' => 'MIT',
        'requires' => 'Python 3.9+',
        'language' => 'Python',
        'stack' => ['Python', 'SQLite', 'Claude Code plugin', 'Claude skills', 'Hooks'],

        'highlights' => [
            ['Plain-words notes', 'Write "end of next month" or "the 3rd Friday of May". Claude resolves it to a real date; a note with no date is saved as none.'],
            ['Session-start brief', 'A hook shows overdue, due-today and upcoming notes when a session starts, and hands the same text to Claude as context.'],
            ['Local and private', 'One SQLite file at ~/.notescos/notes.db. No account, no network calls, standard library only.'],
            ['You stay in control', 'Adding and closing notes can only be done by you, so Claude never saves or completes a note on its own.'],
        ],

        'install' => [
            'claude plugin marketplace add aliyanfaisal/notemind',
            'claude plugin install notemind@notemind',
        ],
        'try' => '/notemind:note-add call Sara tomorrow at 3pm',

        'commands' => [
            ['/notemind:note-add', 'Add a note in plain words.', true],
            ['/notemind:note-today', 'Show what is overdue, due today and coming up.', false],
            ['/notemind:note-list', 'List notes, including finished ones or filtered by project or type.', false],
            ['/notemind:note-done', 'Mark a note as done.', true],
            ['/notemind:note-help', 'Explain the commands and how to use them.', false],
        ],
        'commands_note' => 'Commands marked "You only" cannot be triggered by Claude. The others can also be used by Claude when you ask something like "what is due?".',

        'how_it_works' => [
            ['Date interpretation', 'The note-add skill asks Claude to work out the due date from your sentence. The current date and time go into the prompt, and the result is saved as YYYY-MM-DD or YYYY-MM-DDTHH:MM. A rule-based parser lives in core/notescos/parse.py.'],
            ['Storage', 'SQLite with versioned, append-only migrations (store.py). The database is not removed when you uninstall the plugin.'],
            ['The brief', 'brief.py groups notes into overdue, due today, upcoming and no date. hooks.py runs it from one SessionStart hook.'],
            ['Quiet by design', 'The hook stays silent when nothing is overdue, due today or due within 2 days, on compact events, and for notes set to silent. Errors are logged and swallowed so it can never break a session. A run takes about 85 ms.'],
            ['Least privilege', 'Each skill is limited to running the bundled tool, and the two that change data are user-invocable only.'],
        ],
        'architecture' => [
            ['.claude-plugin/', 'plugin.json and marketplace.json: the plugin manifest and marketplace.'],
            ['skills/', 'note-add, note-today, note-list, note-done and note-help.'],
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

        'roadmap' => [
            'Editing notes in place (today you add a corrected note and mark the old one done)',
            'Money tracking with amount and currency',
            'Snoozing and reminders',
            'Priority and urgent or silent alert levels that shape the banner',
            'Filtering and grouping by person, project, git branch and tags',
            'Notes captured from sessions and other tools, reviewed before they count',
            'Richer filtering by type and project, and --json output for scripting',
        ],
    ],
];
