<?php

/*
| All site content lives here. Pages are plain Blade templates that render this data, so editing the
| site means editing this file. Entries with 'file' => null render as "coming soon".
*/
return [
    'person' => [
        'name' => 'Aliyan Faisal',
        'title' => 'AI Engineer & Aspiring Researcher',
        'location' => 'Islamabad, Pakistan',
        'email' => 'research@aliyanfaisal.com',
        'github' => 'https://github.com/aliyanfaisal',
        'linkedin' => 'https://www.linkedin.com/in/aliyan-faisal-5162261b7/',
        'cv_file' => 'documents/aliyan-faisal-cv.pdf', // inside public/. Not in a folder named cv/, which would shadow the /cv page. The download link only appears when the file exists.
        'summary' => [
            'I am an AI engineer with a BSc in Information Technology (CGPA 3.80 / 4.00) and several years of experience building applied AI systems: prompt tooling, retrieval-augmented generation, document understanding and the backend infrastructure around them.',
            'I am applying to research-oriented Master\'s programs in Artificial Intelligence and Machine Learning. My goal is to move from building LLM-based systems to studying why they work, where they fail, and how to make them more reliable and efficient.',
        ],
    ],

    'interests' => [
        [
            'name' => 'LLM reliability and evaluation',
            'text' => 'A system that is right most of the time is only useful if it is clear about the rest. In my own projects I separate what a model reads from what is verified by deterministic code, attach confidence and evidence to every value, and allow a "could not verify" answer. I want to study this more rigorously.',
            'questions' => [
                'What does a good evaluation harness look like for extraction and error-detection tasks?',
                'How should confidence be calibrated and shown to users?',
            ],
            'related' => ['invoiceinspect', 'manajet'],
        ],
        [
            'name' => 'Uncertainty, calibration and abstention',
            'text' => 'My tools return "could not verify" when two readings of a document disagree. That works, but the decision is a hand-set rule. I want to understand when a model\'s own confidence can be trusted, how to calibrate it, and when a system should decline to answer instead of guessing.',
            'questions' => [
                'When two independent extractors disagree, can the disagreement itself be used as a calibrated confidence signal?',
                'How should a system trade off wrong answers against "I could not tell", and how should that trade-off be measured?',
            ],
            'related' => ['invoiceinspect'],
        ],
        [
            'name' => 'Evaluation methodology and test-set leakage',
            'text' => 'While evaluating InvoiceInspect I found how easily a score gets inflated: fixing problems on a test set makes that set stop being a fair test. I now label every score as tuned or fresh. I am interested in the methods behind trustworthy evaluation of applied LLM systems, not only in the numbers.',
            'questions' => [
                'How much does tuning on a test set inflate reported accuracy, and how can that be detected and reported?',
                'How should synthetic test data with planted errors be built so that results transfer to real documents?',
            ],
            'related' => ['invoiceinspect'],
        ],
        [
            'name' => 'Efficient and small language models',
            'text' => 'Large models are expensive to run and often out of reach for individuals and small teams. I am interested in how much quality can be kept with small, quantized or local models, and in the systems side of running them on ordinary hardware. I run local models and Linux servers myself, and my coursework in operating systems, networks and distributed systems covers the systems side.',
            'questions' => [
                'What is the best quality a small or quantized model can reach on a given task using commodity hardware?',
                'Which steps of a pipeline can use a smaller or local model without hurting the result, and can routing between models be decided from the input?',
            ],
            'related' => ['cuelara'],
        ],
        [
            'name' => 'Misinformation and fact-checking in low-resource languages',
            'text' => 'Most work on detecting false claims is done in English. Urdu is my native language, and I want to study how well current language models can check claims in Urdu and in languages with little data, where misinformation spreads and tools are scarce. I have not built a project in this area yet.',
            'questions' => [
                'How well do current models verify claims in Urdu and in mixed Urdu-English text, compared with English?',
                'What evaluation data and sources of evidence are missing for fact-checking in low-resource languages?',
            ],
            'related' => [],
        ],
        [
            'name' => 'Retrieval-augmented generation (RAG)',
            'text' => 'RAG is how most LLM systems get grounded in real documents. I want to understand the parts that decide whether it works: document parsing, chunking, embeddings, retrieval quality and how a model should behave when the evidence is weak or contradictory.',
            'questions' => [
                'How do chunking and retrieval choices affect answer faithfulness?',
                'How should a system report that it could not find enough evidence?',
            ],
            'related' => ['cuelara'],
        ],
        [
            'name' => 'Prompt engineering and token efficiency',
            'text' => 'Prompts are the interface to every LLM application, and most are written by trial and error. I am interested in treating prompts as something that can be measured, compressed and debugged: how much context a model needs, what can be removed without changing the answer, and how to score a prompt before it is run.',
            'questions' => [
                'How much of a prompt can be removed or restructured before output quality drops?',
                'Can the reasons a prompt fails be diagnosed automatically and explained to the user?',
            ],
            'related' => ['cuelara'],
        ],
        [
            'name' => 'Multimodal document understanding',
            'text' => 'Real documents are scans, tables and layouts, not clean text. I have worked with OCR and vision models alongside text extraction, cross-checking independent readings of the same page, and I am interested in how these signals should be combined.',
            'questions' => [
                'When two independent readings of a document disagree, which should be trusted and why?',
            ],
            'related' => ['invoiceinspect'],
        ],
        [
            'name' => 'Data-centric systems for machine learning',
            'text' => 'Much of the work in applied AI is data pipelines, schemas, storage and decision-support tooling. My work for a provincial planning department on data collection and analytics dashboards gave me a view of how data quality shapes what any model can do.',
            'questions' => [
                'How do structured data pipelines and validation affect downstream model quality?',
            ],
            'related' => ['gov-dashboard'],
        ],
        [
            'name' => 'AI for climate and disaster resilience in mountain regions',
            'text' => 'I studied and worked in Gilgit-Baltistan, a mountain region exposed to floods, glacial lake outbursts and landslides, where data is sparse and planning depends on it. I am interested in whether low-cost AI methods can support early warning and planning under those conditions. My related experience is the planning-department data work, not climate modelling, and I would be learning this field.',
            'questions' => [
                'Can low-cost models give useful early warnings or risk estimates where sensor and historical data are sparse?',
                'How should uncertainty be communicated to planners and communities who must act on a prediction?',
            ],
            'related' => ['gov-dashboard'],
        ],
    ],

    'projects' => [
        'invoiceinspect' => [
            'name' => 'InvoiceInspect',
            'tagline' => 'Find invoice errors before you pay.',
            'blurb' => 'Cross-checks two independent readings of an invoice (PDF text and a vision model) and recalculates every number in code, flagging what it cannot verify.',
            'period' => '2025 – present',
            'role' => 'Creator and lead engineer',
            'status' => 'Live',
            'url' => 'https://invoiceinspect.app',
            'image' => 'images/projects/invoiceinspect.webp',
            'summary' => 'An online tool that checks invoices automatically. A user uploads a PDF and gets every arithmetic or consistency error back with the evidence behind it.',
            'problem' => 'Businesses pay invoices that contain wrong totals, wrong tax, missing details or inconsistent dates, and checking them by hand is slow. Using an LLM alone is risky, because it can read a number wrongly or hide a real error.',
            'what' => [
                ['Automated invoice checks', 'Line totals, subtotal, tax, grand total, required details and date logic are recalculated and compared with what is printed.'],
                ['Evidence for every finding', 'Each result is passed, warning, error or "could not verify", with the expected value, the printed value, the difference and the page it came from.'],
                ['Plain-language explanation', 'A language model explains each finding and suggests a next step. It is always labelled as AI and cannot hide an error on its own.'],
            ],
            'approach' => [
                'Two independent readings of the invoice are produced: the PDF text layer (OCR for scans) and a vision model reading the page images.',
                'Deterministic code, not the model, recalculates every number. If the two readings agree, confidence is high. If they disagree, the value is marked "could not verify" and no error is raised from it.',
                'Every value carries a confidence score and evidence. Rules only fail on high-confidence values, and low-confidence values are shown as "please confirm".',
            ],
            'stack' => ['Laravel 13', 'PHP 8.3', 'PostgreSQL', 'Tesseract OCR', 'Vision model', 'Tailwind CSS 4'],
            'limits' => 'It checks arithmetic and internal consistency. It does not prove that an invoice is genuine, and it is not accounting, tax or legal advice.',
            'evaluation' => [
                'title' => 'Evaluation',
                'note' => 'Measured with my own evaluation harness on separate invoice sets (corpora), each with planted errors. Scores on a corpus used for tuning are marked and are not a fresh measure. These runs replace the earlier figure of about 82% on 70 invoices.',
                'metrics' => [
                    ['Baseline, rules extractor (Corpus A)', '84.5% field accuracy', '25 of 32 planted errors found, 30 false alarms'],
                    ['Unseen multi-page invoices (Corpus C, 40 invoices)', '72.2% field accuracy', '13 of 20 planted errors found, 50 false alarms'],
                    ['Fresh set, rules extractor (Corpus D, 30 invoices)', '62.0% field accuracy', '15 of 26 planted errors found, 30 false alarms'],
                    ['Fresh set, vision extractor, first instruction (Corpus D, 30 invoices)', '99.4% field accuracy', '26 of 26 planted errors found, 0 false alarms; an improved instruction later scored 99.8% on this set but was written after seeing its mistakes, so that figure is not fresh'],
                    ['Rules extractor after general improvements (Corpus D, tuned on this set)', '98.9% field accuracy', '26 of 26 planted errors found, 8 false alarms; improvements were made on these invoices, so this is not a fresh measure'],
                    ['Fresh set, rules extractor (Corpus E, 30 new layouts)', '70.3% field accuracy', '14 of 27 planted errors found, 53 false alarms; nothing was changed beforehand'],
                    ['Fresh set, vision extractor (Corpus E, 30 new layouts)', '97.9% field accuracy', '27 of 27 planted errors found, 8 false alarms'],
                    ['Rules extractor after improvements (Corpus E, all 50 invoices)', '99.9% field accuracy', '37 of 37 planted errors found, 2 false alarms; fixes were made on 40 of the 50, so this is not a fresh measure'],
                    ['Rules extractor after improvements (Corpus E, 10 held-out invoices)', '100% field accuracy', 'Not tuned on, but a small sample (was 64.9% before the improvements)'],
                ],
            ],
            'planned' => 'Next: a contract-matching module that uses retrieval-augmented generation to compare an invoice with the terms of its contract. It is in design and is not part of the current product.',
            'research' => 'This project is the clearest example of my interest in reliability: separating reading from verification, cross-checking independent signals, and being explicit about what could not be verified.',
            'reports' => [],
            'report_links' => [
                ['Research report', 'https://invoiceinspect.app/research/InvoiceInspect-Research-Report.pdf'],
                ['Evaluation harness reports', 'https://invoiceinspect.app/eval-harness-reports'],
            ],
        ],

        'cuelara' => [
            'name' => 'Cuelara',
            'tagline' => 'Tools for people whose prompts are not working.',
            'blurb' => 'A prompt debugger, optimizer and token-reduction toolkit with an embedding-matched prompt book, also available through an API and an MCP server.',
            'period' => '2025 – present',
            'role' => 'Creator and lead engineer',
            'status' => 'Live',
            'url' => 'https://cuelara.com',
            'image' => 'images/projects/cuelara.webp',
            'summary' => 'A web toolkit and prompt book for people who use ChatGPT, Claude or Gemini and cannot get the answers they want, or whose prompts use more tokens than they should.',
            'problem' => 'Many LLM users write prompts by trial and error. The prompts fail without explanation, cost more tokens than necessary, and cannot be reused. Starting a prompt from a document or from a website\'s design is slow to do by hand.',
            'what' => [
                ['Fixing prompts that do not work', 'A prompt debugger, optimizer and formatter that find why a prompt keeps failing, sharpen it and clean it up. A score shows how a prompt can be improved.'],
                ['Reducing token use', 'A token optimizer compresses a prompt, and a compare-and-estimate tool compares two versions and estimates cost.'],
                ['Creating a prompt from a document', 'The user uploads a PDF or DOCX and the relevant parts are pulled out with retrieval and turned into a prompt.'],
                ['Creating a design prompt from a website', 'A website\'s design or content is turned into a prompt that can be given to an AI tool.'],
            ],
            'approach' => [
                'The user describes the problem in their own words. Text embeddings match that description to the right tool and to relevant prompts in the prompt book, and the tool runs on the same page.',
                'The same tools are available through a public API and an MCP server, so they can be used from Claude, Cursor and other clients, and through a browser extension that works on sites the user already uses.',
                'Usage is controlled with rate limiting based on the plan a user has bought, so each plan has its own limits.',
            ],
            'stack' => ['Next.js 16', 'React 19', 'TypeScript', 'Prisma 7', 'PostgreSQL', 'Text embeddings', 'MCP'],
            'research' => 'Cuelara is where I became interested in prompt optimization and token reduction as research questions: what makes a prompt effective, how it can be measured, and how much context is actually needed.',
            'reports' => [],
        ],

        'manajet' => [
            'name' => 'ManaJet',
            'tagline' => 'AI-assisted project and task management for small software teams.',
            'blurb' => 'My final year project: project, team, task and client management for small software companies, with AI-assisted task planning.',
            'period' => '2022 – 2023',
            'role' => 'Lead developer · final year project, team of two',
            'status' => 'Final year project',
            'url' => null,
            'repo' => 'https://github.com/aliyanfaisal/ManaJet-Showcase',
            'image' => 'images/projects/manajet.webp',
            'summary' => 'A project management system built with one teammate as our final year project, to help small software companies manage projects, teams, tasks and clients in one place.',
            'problem' => 'Small software companies often track projects, tasks, bugs and client requests across several tools. They need something simple that covers the whole workflow without the cost or complexity of a large platform.',
            'what' => [
                ['Projects, teams and tasks', 'Projects with categories, budgets and live progress; teams with a lead; ordered tasks with deadlines, priorities, a responsible member and attachments.'],
                ['Roles and permissions', 'Project managers, team members and clients see different things. Members see only their own teams\' work, and clients follow their own project and raise tickets.'],
                ['Tickets, chat and notifications', 'Bugs and change requests against a project or a task, one-to-one chat, a notice board and optional WhatsApp alerts.'],
                ['Dashboard and analytics', 'Totals, income from completed projects, the best-performing team and charts by priority and month.'],
                ['AI task planner', 'An optional assistant suggests an Agile-style task list for a project, with a description, priority and estimated days for each task.'],
            ],
            'approach' => [
                'The planner uses an OpenAI model. Its output is constrained with a JSON schema and prompt rules and validated before it is stored, so the rest of the system can rely on a predictable structure.',
                'The backend is a relational model of projects, teams, tasks and dependencies, exposed through REST endpoints.',
            ],
            'stack' => ['Laravel', 'MySQL', 'OpenAI API', 'Twilio WhatsApp', 'Client portal'],
            'limits' => 'This was an engineering project, not a research study. The project report covers functional test cases only, with no performance or security test results, and I did not measure how good the AI-generated task lists are. A formal evaluation of the planner is the obvious next step.',
            'research' => 'ManaJet was my first use of an LLM inside a real product, and the first time I had to make model output structured and predictable enough to store.',
            'reports' => [],
        ],

        'gov-dashboard' => [
            'name' => 'Provincial data dashboard',
            'tagline' => 'Data collection and analytics for a government planning department.',
            'blurb' => 'Dashboard and data-management system for the Planning & Development Department of Gilgit-Baltistan, built to collect and analyse provincial data.',
            'period' => '2024 – 2025',
            'role' => 'Backend developer and data analyst',
            'status' => 'Delivered',
            'url' => null,
            'image' => null,
            'summary' => 'A dashboard and data-management system for the Planning & Development Department of the Government of Gilgit-Baltistan, built to collect, manage and analyse provincial data.',
            'problem' => 'Project and planning data was collected on paper and in spreadsheets, which made it slow to gather, hard to keep consistent and difficult to analyse across the province.',
            'what' => [
                ['Centralized data management', 'Records from spreadsheets (XLS, CSV) and a MySQL database were brought into one system.'],
                ['Analytics dashboards', 'Dashboards over the public-sector records give planners an overview of the data.'],
                ['Digitized workflows', 'Paper-based administrative workflows were turned into structured reporting that supports regional planning decisions.'],
            ],
            'approach' => [
                'Python (Pandas) was used to clean and analyse the data, with a Laravel backend for storage, workflows and the dashboards.',
            ],
            'stack' => ['Python', 'Pandas', 'Laravel', 'MySQL', 'XLS / CSV'],
            'research' => 'This work showed me how much the quality of a dataset limits what any model built on it can do, and is the source of my interest in data-centric approaches.',
            'reports' => [],
        ],
    ],

    'open_source' => [
        'notemind' => [
            'name' => 'Notemind',
            'tagline' => 'Notes in plain words, right inside Claude Code.',
            'status' => 'Open source · MIT',
            'version' => '0.7.1',
            'license' => 'MIT',
            'requires' => 'Python 3.9+',
            'repo' => 'https://github.com/aliyanfaisal/notemind',
            'summary' => 'A free plugin for developers who use Claude Code. Notes are added straight from the chat, saved in a central local database, and the user is reminded of what is due or overdue each time a session starts.',
            'highlights' => [
                ['Plain-words notes', 'Write "end of next month" or "the 3rd Friday of May". Claude resolves it to a real date; a note without a date is saved as none.'],
                ['Session-start brief', 'A hook shows overdue, due-today and upcoming notes when a session starts, and gives the same text to Claude as context.'],
                ['Local and private', 'One SQLite file on the user\'s machine. No account, no network calls, standard library only.'],
                ['Least privilege', 'Adding and closing notes can only be done by the user, so Claude never saves or completes a note on its own. Each skill is limited to running the bundled tool.'],
            ],
            'install' => [
                'claude plugin marketplace add aliyanfaisal/notemind',
                'claude plugin install notemind@notemind',
            ],
            'commands' => [
                ['/notemind:note-add', 'Add a note in plain words (user only).'],
                ['/notemind:note-today', 'Show what is overdue, due today and coming up.'],
                ['/notemind:note-list', 'List notes, including finished ones, or filter by project or type.'],
                ['/notemind:note-done', 'Mark a note as done (user only).'],
                ['/notemind:note-help', 'Explain the commands.'],
            ],
            'how' => [
                'The date is worked out by Claude from the user\'s sentence, with the current date and time included in the prompt, and saved as YYYY-MM-DD or YYYY-MM-DDTHH:MM. A rule-based parser sits alongside it.',
                'Notes are stored in SQLite with versioned, append-only migrations. A single SessionStart hook groups notes into overdue, due today, upcoming and no date, and stays silent when nothing is due within two days. Errors are logged and swallowed so the hook can never break a session. A run takes about 85 ms.',
                'The core is about 900 lines of Python in four modules (storage, brief, hooks and CLI).',
            ],
            'roadmap' => [
                'Editing notes in place',
                'Money tracking with amount and currency',
                'Snoozing and reminders',
                'Priority and urgency levels',
                'Grouping by person, project, git branch and tags',
                'Notes captured from sessions and other tools, reviewed before they count',
            ],
        ],
    ],

    'reports' => [
        [
            'project' => 'invoiceinspect',
            'title' => 'InvoiceInspect: research report and evaluation harness reports',
            'summary' => 'The research question behind InvoiceInspect, and eight test reports from my evaluation harness. Each report reuses the invoices and commands of the one before, so results can be compared. The reports are hosted on invoiceinspect.app.',
            'file' => null,
            'available' => true,
            'index_url' => 'https://invoiceinspect.app/eval-harness-reports',
            'documents' => [
                ['Research Report', 'The research question, how the tool is built to answer it, what the measurements show and where it falls short.', 'https://invoiceinspect.app/research/InvoiceInspect-Research-Report.pdf'],
                ['Evaluation Harness: Complete Guide', 'How the harness works end to end, the five extractors it can score, how to read results and its limits.', 'https://invoiceinspect.app/harness-reports/01-Evaluation-Harness-Guide.pdf'],
                ['Report 1: Baseline', 'The tool as it stands on two invoice sets. Corpus A: 84.5% field accuracy, 25 of 32 planted errors found, 30 false alarms. Corpus B: 99.9%, 20 of 20, 0 false alarms.', 'https://invoiceinspect.app/harness-reports/02-Test-Report-1-Baseline.pdf'],
                ['Report 2: Baseline after fixes', 'Six reading problems fixed and the same invoices rescored. Corpus A rose to 98.1% (31 of 32, 6 false alarms), but it was used for tuning, so it reads higher than a fresh set would.', 'https://invoiceinspect.app/harness-reports/03-Test-Report-2-After-Fixes.pdf'],
                ['Report 3: Multi-page invoice testing and issues found', '40 new, unseen multi-page invoices (Corpus C): 72.2% field accuracy, 13 of 20 planted errors found, 50 false alarms.', 'https://invoiceinspect.app/harness-reports/04-Multi-Page-Invoice-Testing-and-Issues-Found.pdf'],
                ['Report 4: Multi-page invoice fixes', 'Report 3 problems fixed and rescored: 99.98%, 20 of 20, 0 false alarms. The fixes were made on these invoices, so this does not show performance on other invoices. Older sets rescored to check for regressions: Corpus A 99.07% (was 98.13% in Report 2; 31 of 32 planted errors found, 4 false alarms) and Corpus B 99.91% (unchanged; 20 of 20, no false alarms).', 'https://invoiceinspect.app/harness-reports/05-Multi-Page-Invoice-Fixes.pdf'],
                ['Report 5: Fresh invoice test, rules and vision model', '30 new invoices (Corpus D). Rules extractor: 62.0%, 15 of 26, 30 false alarms. Vision extractor (first instruction): 99.4%, 26 of 26, 0 false alarms. An improved instruction scored 99.8% on the same set, but it was written after seeing that set\'s mistakes, so 99.8% is not a fresh measure.', 'https://invoiceinspect.app/harness-reports/06-Fresh-Invoice-Test-Rules-and-Vision.pdf'],
                ['Report 6: Rules extractor general improvements', 'General changes to how the rules extractor reads text, made to close the gaps Report 5 found, scored on the same 30 invoices (Corpus D). Rules extractor: 98.9% (was 62.0%), 26 of 26 planted errors found (was 15), 8 false alarms (was 30). The changes were made on these invoices, so this shows the gaps are closed, not how other invoices would read. Older sets rescored: Corpus A 99.01%, B 99.83% and C 99.98% (were 99.07%, 99.91% and 99.98% in Report 4), with all planted errors still found and no new false alarms.', 'https://invoiceinspect.app/harness-reports/07-Rules-Extractor-General-Improvements.pdf'],
                ['Report 7: Fresh test, 50 invoices', 'A fresh set of 50 invoices (Corpus E): 20 from the generator and 30 in new page structures, countries, languages and number formats, with nothing changed beforehand. On the 30 new layouts the rules extractor scored 70.3% field accuracy (14 of 27 planted errors found, 53 false alarms) and the vision extractor 97.9% (27 of 27, 8 false alarms).', 'https://invoiceinspect.app/harness-reports/08-Fresh-Test-50-Invoices.pdf'],
                ['Report 8: Rules extractor improvements on Corpus E', 'Improvements after Report 7 (currency signs, label words in more languages, date formats, some structures). Fixes were made on 40 of the 50 invoices; 10 were held back as an untuned check. All 50: 99.9% field accuracy (was 92.2%), 37 of 37 planted errors found (was 24), 2 false alarms (was 53). Held-out 10: 100% (was 64.9%), a small sample. Corpora A, B and C are unchanged from Report 6; Corpus D went from 98.94% to 99.35% with false alarms down from 8 to 4.', 'https://invoiceinspect.app/harness-reports/09-Rules-Extractor-Improvements-on-Corpus-E.pdf'],
            ],
            'extra_links' => [
                ['All test invoices (every corpus)', 'https://invoiceinspect.app/eval-harness-reports/invoices'],
            ],
        ],
    ],

    'education' => [
        'school' => 'Karakoram International University',
        'place' => 'Gilgit, Pakistan',
        'degree' => 'Bachelor of Science in Information Technology',
        'years' => '2019 – 2023',
        'cgpa' => '3.80 / 4.00',
        'rank' => 'Ranked 1st in class in all 8 semesters of the BSIT program',
        'transcript_file' => 'documents/aliyan-faisal-transcript.pdf', // inside public/. The link only appears when the file exists.
        'scholarship' => 'HEC Ehsaas Scholarship (fully funded for the entire degree)',
        'coursework' => [
            'Data Structures & Algorithms', 'Linear Algebra', 'Probability & Statistics', 'Database Systems',
            'Operating Systems', 'Computer Networks', 'Distributed Systems', 'Software Engineering',
        ],
        'focus' => 'Applied computing, system architecture, database design and algorithmic problem-solving.',
    ],

    // Shown on /achievements. Only claims that can be backed by a document or a public profile.
    'achievements' => [
        'freelance' => [
            'reviews' => '230+', // Fiverr 207 + Upwork 24 when checked on 2026-10-22. Raise only with a verifiable count.
            'text' => 'client reviews across Fiverr and Upwork, from more than five years of independent engineering work for international clients.',
        ],
        'other' => [
            ['Certificate of Appreciation, Ismaili Boy Scouts Prince Town', 'For a contribution to volunteerism.'],
            ['Merit Certificate, Public Schools & Colleges Jutial (2017)', 'For performing at the opening and closing ceremony of Annual Sports 2017 as a member of the College Band Team.'],
        ],
    ],

    'experience' => [
        ['role' => 'Founder & CEO', 'org' => 'UrbanSofts, a remote software development team', 'place' => 'Remote', 'years' => '2023 – present', 'text' => 'Founded and lead a remote team of 4–10 developers that delivers client software projects together: I take on projects and bring in the right team members for each, such as app developers, and we build them collaboratively. The team has served 50+ clients.'],
        ['role' => 'Backend Developer & Data Analyst', 'org' => 'Planning & Development Department, Government of Gilgit-Baltistan', 'place' => 'Gilgit, Pakistan', 'years' => '2024 – 2025 (4–5 months)', 'text' => 'Built centralized data analytics dashboards over public-sector records (XLS, CSV, MySQL) using Python (Pandas) and a Laravel backend, and digitized paper workflows into structured reporting for regional planning.'],
        ['role' => 'Full Stack Web Developer (Team Lead)', 'org' => 'GreyMatter Ventures', 'place' => 'Gilgit, Pakistan, part-time', 'years' => '2021 – 2023', 'text' => 'Designed backend architectures, relational schemas and server-side logic, and led the web development team, mentoring junior developers and remote interns.'],
        ['role' => 'Applied AI & Backend Engineer', 'org' => 'Freelance', 'place' => 'Remote', 'years' => '2020 – present (5+ years)', 'text' => 'Built production applications integrating LLM APIs (OpenAI, Claude, Gemini) for document summarization, data extraction and e-commerce processing.'],
    ],

    'skills' => [
        ['AI engineering & RAG', 'LLM APIs (OpenAI, Gemini, Anthropic, Groq, DeepSeek), Local LLMs, Tool and Function Calling, Retrieval-Augmented Generation (RAG), Vector Embeddings, Prompt Optimization, Context Window Management, Token Reduction'],
        ['Languages & backend', 'Python, PHP (Laravel), Node.js, Flask, TypeScript, JavaScript, RESTful APIs, SQL'],
        ['Data & infrastructure', 'Pandas, NumPy, PostgreSQL, MySQL, MongoDB, Vector Databases, Docker, Git, Linux Administration'],
    ],

    'interests_note' => 'More broadly, I would like to help with research on AI and its impact on people and society: how these systems behave across languages and communities, who benefits, who is left out, and how their risks can be measured and reduced. I am glad to contribute engineering, data collection and evaluation work to such projects while I learn from the researchers leading them.',

    'languages' => 'Urdu (native), English (professional working proficiency; proficiency test planned)',
];
