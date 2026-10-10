<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PageController extends Controller
{
    public function home()
    {
        return view('home', $this->shared());
    }

    public function research()
    {
        return view('research', $this->shared());
    }

    public function projects()
    {
        return view('projects.index', $this->shared());
    }

    public function project(string $slug)
    {
        $project = config("research.projects.$slug");
        abort_if($project === null, 404);

        return view('projects.show', $this->shared() + ['project' => $project, 'slug' => $slug]);
    }

    public function openSource()
    {
        return view('open-source.index', $this->shared());
    }

    public function openSourceProject(string $slug)
    {
        $item = config("research.open_source.$slug");
        abort_if($item === null, 404);

        return view('open-source.show', $this->shared() + ['item' => $item, 'slug' => $slug]);
    }

    public function reports()
    {
        return view('reports', $this->shared());
    }

    public function cv()
    {
        return view('cv', $this->shared());
    }

    public function contact()
    {
        return view('contact', $this->shared());
    }

    public function cvFile(): BinaryFileResponse
    {
        $path = public_path(config('research.person.cv_file'));
        abort_unless(File::exists($path), 404);

        return response()->file($path, ['Content-Type' => 'application/pdf']);
    }

    public function sitemap(): Response
    {
        $urls = collect([route('home'), route('research'), route('projects.index'), route('open-source.index'), route('reports'), route('cv'), route('contact')])
            ->merge(collect(config('research.projects'))->keys()->map(fn ($slug) => route('projects.show', $slug)))
            ->merge(collect(config('research.open_source'))->keys()->map(fn ($slug) => route('open-source.show', $slug)));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .$urls->map(fn ($url) => '  <url><loc>'.e($url).'</loc></url>')->implode("\n")."\n</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * @return array<string, mixed>
     */
    private function shared(): array
    {
        return [
            'person' => config('research.person'),
            'hasCv' => File::exists(public_path(config('research.person.cv_file'))),
        ];
    }
}
