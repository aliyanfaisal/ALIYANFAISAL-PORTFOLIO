<?php

use App\Http\Controllers\Api\AutomationLogController;
use App\Http\Controllers\Api\BlogPostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\LinkedInPostController;
use Illuminate\Support\Facades\Route;

Route::post('/blog-posts', [BlogPostController::class, 'store'])
    ->middleware('blog.api.token')
    ->name('api.blog-posts.store');

// Must stay ahead of the {blogPost} wildcard routes below, or "recent-images" would be
// resolved as a slug instead.
Route::get('/blog-posts/recent-images', [BlogPostController::class, 'recentImages'])
    ->middleware('blog.api.token')
    ->name('api.blog-posts.recent-images');

Route::get('/blog-posts', [BlogPostController::class, 'index'])
    ->middleware('blog.api.token')
    ->name('api.blog-posts.index');

Route::get('/blog-posts/{blogPost}', [BlogPostController::class, 'show'])
    ->middleware('blog.api.token')
    ->name('api.blog-posts.show');

Route::match(['put', 'patch'], '/blog-posts/{blogPost}', [BlogPostController::class, 'update'])
    ->middleware('blog.api.token')
    ->name('api.blog-posts.update');

Route::delete('/blog-posts/{blogPost}', [BlogPostController::class, 'destroy'])
    ->middleware('blog.api.token')
    ->name('api.blog-posts.destroy');

Route::get('/categories', [CategoryController::class, 'index'])
    ->middleware('blog.api.token')
    ->name('api.categories.index');

Route::post('/categories', [CategoryController::class, 'store'])
    ->middleware('blog.api.token')
    ->name('api.categories.store');

Route::get('/categories/{category}', [CategoryController::class, 'show'])
    ->middleware('blog.api.token')
    ->name('api.categories.show');

Route::match(['put', 'patch'], '/categories/{category}', [CategoryController::class, 'update'])
    ->middleware('blog.api.token')
    ->name('api.categories.update');

Route::post('/automation-logs', [AutomationLogController::class, 'store'])
    ->middleware('blog.api.token')
    ->name('api.automation-logs.store');

Route::post('/linkedin-post', [LinkedInPostController::class, 'store'])
    ->middleware('linkedin.api.token')
    ->name('api.linkedin-post.store');

Route::post('/upload-image', [ImageUploadController::class, 'store'])
    ->middleware('blog.api.token')
    ->name('api.upload-image.store');
