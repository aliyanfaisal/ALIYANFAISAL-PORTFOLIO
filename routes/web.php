<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentReactionController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LinkedInAuthController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OpenSourceController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/feed', [BlogController::class, 'feed'])->name('blog.feed');
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{slug}/reactions', [PostReactionController::class, 'store'])->name('blog.reactions.store');
Route::post('/blog/{slug}/comments', [CommentController::class, 'store'])->name('blog.comments.store');
Route::post('/blog/{slug}/comments/{comment}/replies', [CommentController::class, 'reply'])->name('blog.comments.reply');
Route::put('/blog/{slug}/comments/{comment}', [CommentController::class, 'update'])->name('blog.comments.update');
Route::delete('/blog/{slug}/comments/{comment}', [CommentController::class, 'destroy'])->name('blog.comments.destroy');
Route::post('/blog/{slug}/comments/{comment}/reactions', [CommentReactionController::class, 'store'])->name('blog.comments.reactions.store');
Route::get('/about', [AboutController::class, 'index'])->name('about');
// The Projects page was removed; keep old links working.
Route::redirect('/projects', '/', 301);
Route::redirect('/projects/download', '/', 301);
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/open-source', [OpenSourceController::class, 'index'])->name('open-source.index');
Route::get('/open-source/{slug}', [OpenSourceController::class, 'show'])->name('open-source.show');
Route::post('/products/manajet/demo', [DemoRequestController::class, 'store'])->middleware('throttle:5,1')->name('products.demo');
// Freelance services live on their own site. Nothing on this site links to it; old /services URLs
// (and their backlinks) just redirect there permanently.
Route::get('/services/{path?}', fn (?string $path = null) => redirect()->away(rtrim(config('seo.freelance_url'), '/').'/services'.($path ? '/'.$path : ''), 301))->where('path', '.*');
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

Route::get('/auth/linkedin', [LinkedInAuthController::class, 'redirect'])->name('auth.linkedin.redirect');
Route::get('/auth/linkedin/callback', [LinkedInAuthController::class, 'callback'])->name('auth.linkedin.callback');

// Routes unmatched by any of the above never run through the `web` middleware group (session,
// $errors sharing, etc.), since middleware only applies once a route is matched. Falling back to
// an explicit route inside the group ensures the 404 page renders with a fully booted request.
Route::fallback(fn () => abort(404));
