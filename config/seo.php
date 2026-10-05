<?php

/*
| Single source of truth for identity + SEO defaults. Edit your name, role, bio and
| social profiles here and every page, schema node and the RSS feed follows.
*/
return [
    'site_name' => 'Aliyan Faisal',

    'person' => [
        'name' => 'Aliyan Faisal',
        'job_title' => 'Full-Stack Developer & AI / LLM Systems Engineer',
        'tagline' => 'Full-Stack Developer · AI & LLM Systems Engineer',
        'description' => 'Full-stack developer and AI/LLM systems engineer building LLM integrations, RAG pipelines, agentic automations, e-commerce automation and production web apps with Laravel, Node.js, React and WordPress — plus the server and DevOps work to run them.',
        'short_bio' => 'Full-stack developer and AI/LLM systems engineer. I build LLM integrations, RAG pipelines and automations, and the web apps and servers behind them.',
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
            'https://www.upwork.com/freelancers/~01f763ee3322eda908',
            'https://www.fiverr.com/aliyanfaisal',
        ],
        'github' => 'https://github.com/aliyanfaisal',
        'linkedin' => 'https://www.linkedin.com/in/aliyan-faisal-5162261b7/',
    ],

    'default_title' => 'Aliyan Faisal — Full-Stack Developer & AI / LLM Systems Engineer',
    'default_description' => 'Aliyan Faisal is a full-stack developer and AI/LLM systems engineer building LLM integrations, RAG pipelines, automations and production web apps for clients worldwide.',

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
        'about' => 'Meet Aliyan Faisal, a full-stack developer and AI/LLM systems engineer from Islamabad with 5+ years building LLM apps, automations and web platforms.',
        'projects' => 'Explore client projects and open-source repos by Aliyan Faisal: AI and LLM integrations, automation tools, e-commerce builds and full-stack web apps.',
        'services' => 'Hire Aliyan Faisal for LLM and AI integration, RAG chatbots, workflow automation, full-stack development and server setup. 5.0 rated, 200+ reviews, worldwide.',
        'blog' => 'Practical articles on LLM integration, RAG, AI automation, full-stack development and server configuration from Aliyan Faisal.',
    ],
];
