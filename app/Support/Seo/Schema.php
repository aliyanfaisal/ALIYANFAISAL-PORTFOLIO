<?php

namespace App\Support\Seo;

use App\Models\BlogPost;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Builds schema.org nodes as PHP arrays. Nodes reference each other by stable @id so Google
 * resolves everything to the single Person entity.
 */
class Schema
{
    /**
     * @return array<string, string>
     */
    public static function personRef(): array
    {
        return ['@id' => Seo::personId()];
    }

    /**
     * @return array<string, mixed>
     */
    public static function person(): array
    {
        $person = config('seo.person');

        return [
            '@type' => 'Person',
            '@id' => Seo::personId(),
            'name' => $person['name'],
            'url' => url('/'),
            'image' => asset($person['image']),
            'jobTitle' => $person['job_title'],
            'description' => $person['description'],
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $person['address']['locality'],
                'addressCountry' => $person['address']['country'],
            ],
            'knowsAbout' => $person['knows_about'],
            'sameAs' => $person['same_as'],
        ];
    }

    /**
     * A minimal Person node, for pages that only need the @id to resolve.
     *
     * @return array<string, mixed>
     */
    public static function personLite(): array
    {
        return [
            '@type' => 'Person',
            '@id' => Seo::personId(),
            'name' => config('seo.person.name'),
            'url' => route('about'),
            'sameAs' => config('seo.person.same_as'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => Seo::websiteId(),
            'url' => url('/'),
            'name' => config('seo.site_name'),
            'inLanguage' => 'en',
            'publisher' => self::personRef(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function profilePage(string $url, string $name, ?string $description = null): array
    {
        return array_filter([
            '@type' => 'ProfilePage',
            '@id' => $url.'#profilepage',
            'url' => $url,
            'name' => $name,
            'description' => $description,
            'inLanguage' => 'en',
            'isPartOf' => ['@id' => Seo::websiteId()],
            'mainEntity' => self::personRef(),
        ]);
    }

    /**
     * @param  array<int, array{name: string, url: string}>  $crumbs
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $crumbs, string $pageUrl): array
    {
        return [
            '@type' => 'BreadcrumbList',
            '@id' => $pageUrl.'#breadcrumb',
            'itemListElement' => collect($crumbs)->values()->map(fn (array $crumb, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function collectionPage(string $url, string $name, string $description, ?string $breadcrumbId = null, ?array $itemList = null): array
    {
        return array_filter([
            '@type' => 'CollectionPage',
            '@id' => $url.'#webpage',
            'url' => $url,
            'name' => $name,
            'description' => $description,
            'inLanguage' => 'en',
            'isPartOf' => ['@id' => Seo::websiteId()],
            'breadcrumb' => $breadcrumbId ? ['@id' => $breadcrumbId] : null,
            'mainEntity' => $itemList,
        ]);
    }

    /**
     * @param  array<int, array{url: string, name: string}>  $items
     * @return array<string, mixed>
     */
    public static function itemList(array $items): array
    {
        return [
            '@type' => 'ItemList',
            'numberOfItems' => count($items),
            'itemListElement' => collect($items)->values()->map(fn (array $item, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => $item['url'],
                'name' => $item['name'],
            ])->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function blog(): array
    {
        return [
            '@type' => 'Blog',
            '@id' => Seo::blogId(),
            'url' => route('blog.index'),
            'name' => config('seo.site_name').' — Blog',
            'description' => config('seo.descriptions.blog'),
            'inLanguage' => 'en',
            'isPartOf' => ['@id' => Seo::websiteId()],
            'author' => self::personRef(),
            'publisher' => self::personRef(),
        ];
    }

    /**
     * @param  Collection<int, Service>  $services
     * @return array<int, array<string, mixed>>
     */
    public static function services(Collection $services): array
    {
        return $services->map(fn (Service $service): array => array_filter([
            '@type' => 'Service',
            '@id' => route('services.show', $service).'#service',
            'name' => Str::title($service->title),
            'url' => route('services.show', $service),
            'serviceType' => $service->category,
            'description' => Str::limit(trim(preg_replace('/\s+/', ' ', (string) $service->description)), 300),
            'image' => $service->image_url,
            'provider' => self::personRef(),
            'areaServed' => 'Worldwide',
            'aggregateRating' => $service->rating && $service->rating_count ? [
                '@type' => 'AggregateRating',
                'ratingValue' => (float) $service->rating,
                'reviewCount' => (int) $service->rating_count,
            ] : null,
            'offers' => $service->price_from ? [
                '@type' => 'Offer',
                'price' => (float) $service->price_from,
                'priceCurrency' => 'USD',
                'url' => $service->fiverr_url ?: route('services.show', $service),
            ] : null,
        ]))->all();
    }

    /**
     * @param  Collection<int, Project>  $projects
     * @return array<string, mixed>
     */
    public static function projectList(Collection $projects): array
    {
        return self::itemList($projects->map(fn (Project $project): array => [
            'url' => $project->external_url ?: route('projects.index').'#'.$project->slug,
            'name' => $project->title,
        ])->all());
    }

    /**
     * @param  Collection<int, BlogPost>  $posts
     * @return array<string, mixed>
     */
    public static function postList(Collection $posts): array
    {
        return self::itemList($posts->map(fn (BlogPost $post): array => [
            'url' => route('blog.show', $post),
            'name' => $post->title,
        ])->all());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function blogPosting(BlogPost $post, string $description, array $image): array
    {
        $url = route('blog.show', $post);
        $category = $post->categories->first();

        $crumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Blog', 'url' => route('blog.index')],
        ];
        if ($category) {
            $crumbs[] = ['name' => $category->name, 'url' => route('blog.category', $category)];
        }
        $crumbs[] = ['name' => $post->title, 'url' => $url];

        $posting = array_filter([
            '@type' => 'BlogPosting',
            '@id' => $url.'#article',
            'url' => $url,
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
            'headline' => Str::limit($post->title, 110, ''),
            'description' => $description,
            'image' => [
                '@type' => 'ImageObject',
                'url' => $image['url'],
                'width' => $image['width'],
                'height' => $image['height'],
            ],
            'datePublished' => $post->published_at->toIso8601String(),
            'dateModified' => ($post->updated_at ?? $post->published_at)->toIso8601String(),
            'author' => self::personRef(),
            'publisher' => self::personRef(),
            'isPartOf' => ['@id' => Seo::blogId()],
            'articleSection' => $category?->name,
            'keywords' => $post->tags->pluck('name')->all() ?: null,
            'wordCount' => str_word_count(strip_tags($post->body_html)),
            'inLanguage' => 'en',
        ]);

        return [$posting, self::personLite(), self::breadcrumbs($crumbs, $url)];
    }
}
