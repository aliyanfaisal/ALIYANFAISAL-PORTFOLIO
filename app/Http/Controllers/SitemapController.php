<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Setting;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index(): Sitemap
    {
        $latestPostUpdate = $this->latest(BlogPost::published()->max('updated_at'));

        $sitemap = Sitemap::create()
            ->add($this->url(route('home'), $latestPostUpdate))
            ->add($this->url(route('about'), $this->latest(Setting::query()->max('updated_at'))))
            ->add($this->url(route('products.index')))
            ->add($this->url(route('open-source.index')))
            ->add($this->url(route('blog.index'), $latestPostUpdate))
            ->add($this->url(route('contact.create')));

        foreach (array_keys(config('products')) as $slug) {
            $sitemap->add($this->url(route('products.show', $slug)));
        }

        foreach (array_keys(config('opensource')) as $slug) {
            $sitemap->add($this->url(route('open-source.show', $slug)));
        }

        Category::query()
            ->whereHas('posts', fn ($query) => $query->published())
            ->withMax(['posts as latest_post_update' => fn ($query) => $query->published()], 'updated_at')
            ->orderBy('name')
            ->get()
            ->each(fn (Category $category) => $sitemap->add(
                $this->url(
                    route('blog.category', $category),
                    $this->latest($category->latest_post_update, $category->updated_at),
                )
            ));

        BlogPost::published()
            ->orderByDesc('published_at')
            ->get(['slug', 'title', 'image_path', 'updated_at'])
            ->each(function (BlogPost $post) use ($sitemap): void {
                $url = $this->url(route('blog.show', $post), $post->updated_at);

                if ($post->image_path) {
                    $url->addImage($post->imageUrl(), $post->title);
                }

                $sitemap->add($url);
            });

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
