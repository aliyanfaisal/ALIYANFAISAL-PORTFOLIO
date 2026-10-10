<?php

/*
| Single source of truth for identity + SEO defaults. Edit your name, role, bio and
| social profiles here and every page, schema node and the RSS feed follows.
*/
return [
    'site_name' => 'Aliyan Faisal',

    // Freelance work (Fiverr/Upwork-style services, hiring) lives on its own site.
    'freelance_url' => env('FREELANCE_URL', 'https://freelance.aliyanfaisal.com'),

    'person' => [
        'name' => 'Aliyan Faisal',
        'job_title' => 'Software Engineer & AI Developer',
        'tagline' => 'Software Engineer · AI Developer',
        'description' => 'Software engineer and AI developer from Islamabad, building LLM-powered products, RAG pipelines, automations and production web apps with Laravel, Node.js, React and Python.',
        'short_bio' => 'Software engineer and AI developer. I build LLM-powered products, RAG pipelines and automations, and the web apps behind them.',
        'image' => 'images/aliyan-headshot-cutout.png',
        'address' => ['locality' => 'Islamabad', 'country' => 'PK'],
        'knows_about' => [
            'Full-stack web development', 'LLM integration', 'Retrieval-augmented generation (RAG)', 'AI agents',
            'Workflow automation', 'E-commerce automation', 'OpenAI API', 'Claude API', 'Laravel', 'PHP',
            'Node.js', 'React', 'JavaScript', 'WordPress', 'WooCommerce', 'Shopify', 'REST APIs',
            'Database optimization', 'Server configuration', 'DevOps',
        ],
        'same_as' => [
            'https://github.com/aliyanfaisal',
            'https://www.linkedin.com/in/aliyan-faisal-5162261b7/',
        ],
        'github' => 'https://github.com/aliyanfaisal',
        'linkedin' => 'https://www.linkedin.com/in/aliyan-faisal-5162261b7/',
    ],

    'default_title' => 'Aliyan Faisal — Software Engineer & AI Developer',
    'default_description' => 'Aliyan Faisal is a software engineer and AI developer building LLM-powered products, RAG pipelines, automations and web applications.',

    'default_og_image' => 'images/og-default.png',
    'og_image_width' => 1200,
    'og_image_height' => 630,
    'locale' => 'en_US',
    'robots' => 'index, follow, max-image-preview:large, max-snippet:-1',

    // The only query parameters kept in a canonical URL; utm_*, gclid, q etc. are all dropped.
    'canonical_params' => ['page'],

    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    'bing_site_verification' => env('BING_SITE_VERIFICATION', '3C3AC06C5BDC26DF8A5D4C04239D0370'),
    'ga4_id' => env('GA4_MEASUREMENT_ID'),

    'descriptions' => [
        'about' => 'Meet Aliyan Faisal, a software engineer and AI developer from Islamabad with 5+ years building LLM apps, automations, web platforms and products.',
        'blog' => 'Articles on LLM integration, RAG, AI automation, full-stack development and server configuration from Aliyan Faisal.',
    ],
];
