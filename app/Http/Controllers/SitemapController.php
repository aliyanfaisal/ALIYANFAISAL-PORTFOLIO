<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index(): Sitemap
    {
        $sitemap = Sitemap::create()
            ->add($this->url(route('home'), $this->latest(Service::query()->max('updated_at'))))
            ->add($this->url(route('about'), $this->latest(Setting::query()->max('updated_at'))))
            ->add($this->url(route('projects.index'), $this->latest(Project::query()->max('updated_at'))))
            ->add($this->url(route('products.index')))
            ->add($this->url(route('services.index'), $this->latest(Service::query()->max('updated_at'))))
            ->add($this->url(route('contact.create')));

        foreach (array_keys(config('products')) as $slug) {
            $sitemap->add($this->url(route('products.show', $slug)));
        }

        Service::orderBy('sort_order')->get()->each(fn (Service $service) => $sitemap->add(
            $this->url(route('services.show', $service), $service->updated_at)
        ));

        return $sitemap;
    }

    // Google ignores <changefreq> and <priority>, so only <lastmod> (and images) are emitted.
    private function url(string $location, ?CarbonInterface $lastModified = null): Url
    {
        $url = Url::create($location);

        return $lastModified ? $url->setLastModificationDate($lastModified) : $url;
    }

    /**
     * The most recent of the given timestamps, or null when none are known.
     */
    private function latest(mixed ...$timestamps): ?CarbonInterface
    {
        $dates = collect($timestamps)->filter()->map(fn ($timestamp) => Carbon::parse($timestamp));

        return $dates->isEmpty() ? null : $dates->max();
    }
}
