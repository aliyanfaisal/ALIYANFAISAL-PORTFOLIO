<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * List every category with its description and how many published posts it holds.
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->withCount(['posts as published_posts_count' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $categories->map($this->present(...))->all()]);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($this->present($category->loadCount([
            'posts as published_posts_count' => fn ($query) => $query->published(),
        ])));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'alpha_dash'],
            'description' => ['nullable', 'string', 'max:300'],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['name']);

        // Checked after deriving the slug, so a duplicate *name* is rejected too.
        validator(['slug' => $slug], ['slug' => [Rule::unique('categories', 'slug')]])->validate();

        $category = Category::create([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
        ]);

        return response()->json($this->present($category), 201);
    }

    /**
     * Update a category's name and/or description. The slug is deliberately immutable, since it is
     * part of the public /blog/category/{slug} URL.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:300'],
        ]);

        $category->update($data);

        return response()->json($this->present($category->loadCount([
            'posts as published_posts_count' => fn ($query) => $query->published(),
        ])));
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'url' => route('blog.category', $category),
            'published_posts_count' => $category->published_posts_count ?? 0,
            'updated_at' => $category->updated_at?->toIso8601String(),
        ];
    }
}
