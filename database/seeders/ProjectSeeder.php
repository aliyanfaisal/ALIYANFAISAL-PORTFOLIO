<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Adroitcube',
                'description' => 'Portfolio website for a software development company, built with a clean, conversion-focused layout.',
                'categories' => ['Portfolio', 'Business'],
                'external_url' => 'https://adroitcube.com/',
                'featured' => true,
            ],
            [
                'title' => 'Miami Dream Beach Vacations',
                'description' => 'A vacation rental booking platform with online payments (PayPal, Stripe) and DocuSign integration for digital lease signing.',
                'categories' => ['Booking', 'E-Commerce'],
                'external_url' => null,
                'featured' => true,
            ],
            [
                'title' => 'Castle Milk Rental',
                'description' => 'Booking and e-commerce website for a castle rental business, with a custom storefront and reservation flow.',
                'categories' => ['Booking', 'Business', 'E-Commerce'],
                'external_url' => null,
                'featured' => true,
            ],
            [
                'title' => 'Lingo Lad',
                'description' => 'A language-learning platform combining course content with tech news, built with a distinctive custom design.',
                'categories' => ['Web App', 'Education'],
                'external_url' => null,
                'featured' => false,
            ],
            [
                'title' => 'Ibex Media Network',
                'old_slug' => 'ibex-productions',
                'description' => "Pakistan's digital-first news platform spotlighting under-covered stories in climate justice, gender inclusion and grassroots development.",
                'categories' => ['Media', 'News'],
                'external_url' => 'https://ibexmedianetwork.com/',
                'featured' => false,
            ],
            [
                'title' => 'Good Will Movement',
                'description' => 'Landing page for a charity organization designed to drive awareness and donations.',
                'categories' => ['Charity', 'Landing Page'],
                'external_url' => null,
                'featured' => false,
            ],
            [
                'title' => 'Khalis Pay UK',
                'description' => 'Business and portfolio website for a UK-based payments company.',
                'categories' => ['Business', 'Portfolio'],
                'external_url' => null,
                'featured' => false,
            ],
            [
                'title' => 'Book 24',
                'description' => 'Booking and e-commerce website with a streamlined checkout experience.',
                'categories' => ['Booking', 'E-Commerce'],
                'external_url' => null,
                'featured' => false,
            ],
            [
                'title' => 'Gr2me',
                'description' => "A video and visual-content platform celebrating Greece's culture, heritage and modern lifestyle.",
                'categories' => ['Media', 'Web App'],
                'external_url' => 'https://gr2me.com/',
                'featured' => false,
            ],
            [
                'title' => 'Delos Vacations',
                'description' => 'Luxury Greek travel agency site for bespoke itineraries, private transfers and yacht charter bookings.',
                'categories' => ['Travel', 'Business'],
                'external_url' => 'https://delosvacations.com/',
                'featured' => false,
            ],
            [
                'title' => 'Pharmaquipt',
                'description' => 'Shopify storefront for home medical equipment and mobility supplies.',
                'categories' => ['E-Commerce'],
                'external_url' => 'https://pharmaquipt.com/',
                'featured' => false,
            ],
            [
                'title' => 'What/When',
                'description' => 'Local events and culture discovery platform for Weymouth & Portland, UK.',
                'categories' => ['Web App', 'Directory'],
                'external_url' => 'https://whatwhen.co.uk',
                'featured' => false,
            ],
            [
                'title' => 'Ceramics in Europe',
                'description' => 'EU-wide digital platform connecting ceramic artists with training, entrepreneurship resources and a creative community network.',
                'categories' => ['Web App', 'Education'],
                'external_url' => 'https://www.ceramicsineurope.eu/',
                'featured' => false,
            ],
            [
                'title' => 'The Karakoram',
                'description' => 'Online magazine covering current affairs, tourism, climate and culture across Gilgit-Baltistan and Pakistan.',
                'categories' => ['Media', 'News'],
                'external_url' => 'https://thekarakoram.com.pk/',
                'featured' => false,
            ],
            [
                'title' => 'Nomad',
                'description' => 'Frontend and backend WordPress development work on this premium tech-accessories brand site.',
                'categories' => ['WordPress', 'E-Commerce'],
                'external_url' => 'https://nomadgoods.com/',
                'featured' => false,
            ],
            [
                'title' => 'Damyel',
                'description' => 'WooCommerce storefront for a Parisian chocolaterie selling handcrafted vegan and reduced-sugar chocolates and confectionery.',
                'categories' => ['E-Commerce', 'WordPress'],
                'external_url' => 'https://damyel.com/',
                'featured' => false,
            ],
            [
                'title' => 'Damyel Israel',
                'description' => 'Israeli storefront for the Damyel chocolate brand, localized in Hebrew for the local market.',
                'categories' => ['E-Commerce', 'WordPress'],
                'external_url' => 'https://damyel.co.il',
                'featured' => false,
            ],
            [
                'title' => 'La Femme',
                'description' => 'Booking site for a Norwegian beauty and wellness salon offering nail care, skincare, lash extensions and body treatments.',
                'categories' => ['Booking', 'Business'],
                'external_url' => 'https://la-femme.no/',
                'featured' => false,
            ],
            [
                'title' => 'HomeGenie',
                'description' => 'Romanian services marketplace connecting clients with vetted specialists for cleaning, electrical, plumbing, carpentry and moving jobs.',
                'categories' => ['Web App', 'Marketplace'],
                'external_url' => 'https://www.homegenie.ro/',
                'featured' => false,
            ],
            [
                'title' => 'Bellarui',
                'description' => 'WooCommerce beauty storefront selling premium cosmetics, skincare and personal care products.',
                'categories' => ['E-Commerce', 'WordPress'],
                'external_url' => 'https://bellarui.com/',
                'featured' => false,
            ],
            [
                'title' => 'FlexFrame Eyewear',
                'description' => 'Landing page for a tool-free, snap-on eyeglass frame repair product, with a pre-launch reservation flow powered by Stripe to gauge consumer demand.',
                'categories' => ['Landing Page', 'WordPress'],
                'external_url' => 'https://flexframeeyewear.com/',
                'featured' => false,
            ],
        ];

        foreach ($projects as $i => $project) {
            $newSlug = Str::slug($project['title']);
            $lookupSlug = Project::where('slug', $newSlug)->exists() ? $newSlug : ($project['old_slug'] ?? $newSlug);
            unset($project['old_slug']);
            $project['slug'] = $newSlug;
            $project['sort_order'] = $i;
            Project::updateOrCreate(['slug' => $lookupSlug], $project);
        }
    }
}
