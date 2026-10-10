<?php

/*
| All site content lives here. Pages are plain Blade templates that render this data, so editing the
| site means editing this file. Entries with 'file' => null render as "coming soon".
*/
return [
    'person' => [
        'name' => 'Aliyan Faisal',
        'title' => 'Software engineer · MSc applicant in AI',
        'location' => 'Islamabad, Pakistan',
        'email' => 'research@aliyanfaisal.com',
        'github' => 'https://github.com/aliyanfaisal',
        'linkedin' => 'https://www.linkedin.com/in/aliyan-faisal-5162261b7/',
        'cv_file' => 'cv/aliyan-faisal-cv.pdf', // inside public/. The download link only appears when the file exists.
        'summary' => [
            'I am a software engineer with a BSc in Information Technology (CGPA 3.80 / 4.00) and several years of experience building applied AI systems: prompt tooling, retrieval-augmented generation, document understanding and the backend infrastructure around them.',
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
    ],

    'projects' => [
        'cuelara' => [
            'name' => 'Cuelara',
            'tagline' => 'Tools for people whose prompts are not working.',
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
            'reports' => ['A written report on Cuelara is in preparation.'],
        ],

        'invoiceinspect' => [
            'name' => 'InvoiceInspect',
            'tagline' => 'Find invoice errors before you pay.',
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
                'note' => 'Measured end to end with my own evaluation harness. The test set is small, and I am expanding it and improving the evaluation, so this figure will be updated.',
                'metrics' => [
                    ['Accuracy', '~82%', '70 invoices: 40 from my own generator, 30 generated by Claude'],
                ],
            ],
            'planned' => 'Next: a contract-matching module that uses retrieval-augmented generation to compare an invoice with the terms of its contract. It is in design and is not part of the current product.',
            'research' => 'This project is the clearest example of my interest in reliability: separating reading from verification, cross-checking independent signals, and being explicit about what could not be verified.',
            'reports' => [],
        ],

        'manajet' => [
            'name' => 'ManaJet',
            'tagline' => 'AI-assisted project and task management for small software teams.',
            'period' => '2022 – 2023',
            'role' => 'Lead developer · final year project',
            'status' => 'Final year project',
            'url' => null,
            'repo' => 'https://github.com/aliyanfaisal/ManaJet-Showcase',
            'image' => 'images/projects/manajet.webp',
            'summary' => 'A project management system I built as my final year project, to help small software companies manage projects, teams, tasks and clients in one place.',
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
            'research' => 'ManaJet was my first use of an LLM inside a real product, and the first time I had to make model output structured and predictable enough to store.',
            'reports' => ['The final year project report will be attached here.'],
        ],

        'gov-dashboard' => [
            'name' => 'Provincial data dashboard',
            'tagline' => 'Data collection and analytics for a government planning department.',
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
            'reports' => ['A project report is in preparation. Some details may be limited because the data belongs to a government department.'],
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
            'title' => 'InvoiceInspect: evaluation harness results',
            'summary' => 'Results from my evaluation harness for invoice field extraction and error detection.',
            'file' => null,
            'available' => true,
        ],
        ['project' => 'cuelara', 'title' => 'Cuelara: report', 'summary' => 'In preparation.', 'file' => null, 'available' => false],
        ['project' => 'manajet', 'title' => 'ManaJet: final year project report', 'summary' => 'In preparation.', 'file' => null, 'available' => false],
        ['project' => 'gov-dashboard', 'title' => 'Provincial data dashboard: report', 'summary' => 'In preparation.', 'file' => null, 'available' => false],
    ],

    'education' => [
        'school' => 'Karakoram International University',
        'place' => 'Gilgit, Pakistan',
        'degree' => 'Bachelor of Science in Information Technology',
        'years' => '2019 – 2023',
        'cgpa' => '3.80 / 4.00',
        'scholarship' => 'HEC Ehsaas Scholarship (fully funded for the entire degree)',
        'coursework' => [
            'Data Structures & Algorithms', 'Linear Algebra', 'Probability & Statistics', 'Database Systems',
            'Operating Systems', 'Computer Networks', 'Distributed Systems', 'Software Engineering',
        ],
        'focus' => 'Applied computing, system architecture, database design and algorithmic problem-solving.',
    ],

    'experience' => [
        ['role' => 'Founder & CEO', 'org' => 'UrbanSofts, a remote software development team', 'place' => 'Remote', 'years' => '2022 – present', 'text' => 'Founded and lead a remote team of 4–10 developers that delivers client software projects together: I take on projects and bring in the right team members for each, such as app developers, and we build them collaboratively. The team has served 50+ clients.'],
        ['role' => 'Backend Developer & Data Analyst', 'org' => 'Planning & Development Department, Government of Gilgit-Baltistan', 'place' => 'Gilgit, Pakistan', 'years' => '2024 – 2025 (4–5 months)', 'text' => 'Built centralized data analytics dashboards over public-sector records (XLS, CSV, MySQL) using Python (Pandas) and a Laravel backend, and digitized paper workflows into structured reporting for regional planning.'],
        ['role' => 'Full Stack Web Developer (Team Lead)', 'org' => 'GreyMatter Ventures', 'place' => 'Gilgit, Pakistan, full-time', 'years' => '2020 – 2022', 'text' => 'Designed backend architectures, relational schemas and server-side logic, and led the web development team, mentoring junior developers and remote interns.'],
        ['role' => 'Applied AI & Backend Engineer', 'org' => 'Freelance', 'place' => 'Remote', 'years' => '2020 – present (5+ years)', 'text' => 'Built production applications integrating LLM APIs (OpenAI, Claude, Gemini) for document summarization, data extraction and e-commerce processing.'],
    ],

    'skills' => [
        ['AI engineering', 'LLM APIs (OpenAI, Anthropic, Gemini, Groq, DeepSeek), local LLMs, tool and function calling, retrieval-augmented generation, vector embeddings, prompt optimization, context-window management, token reduction'],
        ['Languages and backend', 'Python, PHP (Laravel), Node.js, Flask, TypeScript, JavaScript, REST APIs, SQL'],
        ['Data and infrastructure', 'Pandas, NumPy, PostgreSQL, MySQL, MongoDB, vector databases, Docker, Git, Linux administration'],
    ],

    'languages' => 'Urdu (native), English (C1)',

    'coming_soon' => [
        'projects' => 'More projects will be added here.',
        'open_source' => 'More open-source work is coming.',
    ],
];
