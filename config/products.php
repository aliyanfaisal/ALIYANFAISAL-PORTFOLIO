<?php

/*
| My own products. Add a new entry here (plus a matching view in resources/views/products/{slug}.blade.php)
| and it appears in the homepage section, the /products index and the sitemap.
*/
return [
    'invoiceinspect' => [
        'name' => 'InvoiceInspect',
        'slug' => 'invoiceinspect',
        'url' => 'https://invoiceinspect.app',
        'domain' => 'invoiceinspect.app',
        'category' => 'Finance · Document AI',
        'tagline' => 'Find invoice errors before you pay.',
        'summary' => 'A free online invoice checker and validator. Upload a PDF and it recalculates the numbers, then shows every mismatch with evidence.',
        'description' => 'InvoiceInspect is a free online invoice checker and validator built by Aliyan Faisal. It recalculates line totals, tax and grand totals on PDF invoices and shows every mismatch with evidence.',
        'image' => 'images/products/invoiceinspect-hero.webp',
        'status' => 'Live',
        'stack' => ['Laravel 13', 'PHP 8.3', 'Tailwind CSS 4', 'PostgreSQL', 'Filament 5', 'Tesseract OCR', 'Vision AI'],
    ],
    'cuelara' => [
        'name' => 'Cuelara',
        'slug' => 'cuelara',
        'url' => 'https://cuelara.com',
        'domain' => 'cuelara.com',
        'category' => 'Developer Tools · Prompt Engineering',
        'tagline' => 'Smart tools and ready-made prompts for any AI.',
        'summary' => 'An AI toolkit and prompt book for people who find prompts hard. Describe a problem in plain words and the right tool runs on the same page.',
        'description' => 'Cuelara is an AI toolkit and prompt book built by Aliyan Faisal: prompt builder, optimizer and debugger tools, a ready-made prompt book, an MCP server and a public API for ChatGPT, Claude and Gemini users.',
        'image' => 'images/products/cuelara-hero.webp',
        'status' => 'Live',
        'stack' => ['Next.js 16', 'React 19', 'TypeScript', 'Prisma 7', 'PostgreSQL', 'Paddle', 'MCP'],
    ],
];
