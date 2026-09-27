<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentReactionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LinkedInAuthController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{slug}/reactions', [PostReactionController::class, 'store'])->name('blog.reactions.store');
Route::post('/blog/{slug}/comments', [CommentController::class, 'store'])->name('blog.comments.store');
Route::post('/blog/{slug}/comments/{comment}/replies', [CommentController::class, 'reply'])->name('blog.comments.reply');
Route::put('/blog/{slug}/comments/{comment}', [CommentController::class, 'update'])->name('blog.comments.update');
Route::delete('/blog/{slug}/comments/{comment}', [CommentController::class, 'destroy'])->name('blog.comments.destroy');
Route::post('/blog/{slug}/comments/{comment}/reactions', [CommentReactionController::class, 'store'])->name('blog.comments.reactions.store');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/download', [ProjectController::class, 'downloadLinks'])->name('projects.download');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

Route::get('/auth/linkedin', [LinkedInAuthController::class, 'redirect'])->name('auth.linkedin.redirect');
Route::get('/auth/linkedin/callback', [LinkedInAuthController::class, 'callback'])->name('auth.linkedin.callback');

// Routes unmatched by any of the above never run through the `web` middleware group (session,
// $errors sharing, etc.), since middleware only applies once a route is matched. Falling back to
// an explicit route inside the group ensures the 404 page renders with a fully booted request.
Route::fallback(fn () => abort(404));
