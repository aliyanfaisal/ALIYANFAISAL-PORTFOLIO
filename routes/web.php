<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\DemoRequestController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/download', [ProjectController::class, 'downloadLinks'])->name('projects.download');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/products/manajet/demo', [DemoRequestController::class, 'store'])->middleware('throttle:5,1')->name('products.demo');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');


// Routes unmatched by any of the above never run through the `web` middleware group (session,
// $errors sharing, etc.), since middleware only applies once a route is matched. Falling back to
// an explicit route inside the group ensures the 404 page renders with a fully booted request.
Route::fallback(fn () => abort(404));
