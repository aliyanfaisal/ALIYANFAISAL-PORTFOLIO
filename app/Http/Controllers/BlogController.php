<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogPostRedirect;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request, category: null);
    }

    public function category(Category $category, Request $request): View
    {
        return $this->listing($request, $category);
    }

    public function feed(): Response
    {
        $posts = BlogPost::published()->with(['categories', 'tags'])->orderByDesc('published_at')->limit(20)->get();

        return response()
            ->view('blog.feed', ['posts' => $posts], 200)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function show(string $slug): View|RedirectResponse
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with(['categories', 'tags', 'reactions', 'comments.reactions', 'comments.replies.reactions'])
            ->first();

        if ($post === null) {
            // A deleted post redirects (301) to wherever still suits it, instead of 404ing away
            // whatever SEO value/backlinks the URL had built up.
            $redirect = BlogPostRedirect::where('from_slug', $slug)->first();

            abort_if($redirect === null, 404);

            return redirect($redirect->to_url, 301);
        }

        $post->setRelation('related', $post->relatedPosts());

        // A page view must not bump updated_at, which feeds the sitemap lastmod and schema dateModified.
        BlogPost::withoutTimestamps(fn () => $post->increment('views'));

        return view('blog.show', ['post' => $post]);
    }

    private function listing(Request $request, ?Category $category): View
    {
        $search = trim((string) $request->query('q'));

        $posts = $this->postsQuery($category, $search)
            ->paginate(15)
            ->withQueryString();

        return view('blog.index', [
            'posts' => $posts,
            'categories' => Category::orderBy('name')->get(),
            'activeCategory' => $category,
            'search' => $search,
        ]);
    }

    private function postsQuery(?Category $category, string $search): Builder
    {
        return BlogPost::published()
            ->with('categories')
            ->when($category, fn ($query) => $query->whereHas(
                'categories',
                fn ($query) => $query->where('categories.id', $category->id)
            ))
            ->when($search !== '', fn ($query) => $query->where(
                fn ($query) => $query->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
            ))
            ->orderByDesc('published_at');
    }
}
