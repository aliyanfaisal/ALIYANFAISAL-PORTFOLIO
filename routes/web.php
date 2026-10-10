<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/research', [PageController::class, 'research'])->name('research');
Route::get('/projects', [PageController::class, 'projects'])->name('projects.index');
Route::get('/projects/{slug}', [PageController::class, 'project'])->name('projects.show');
Route::get('/open-source', [PageController::class, 'openSource'])->name('open-source.index');
Route::get('/open-source/{slug}', [PageController::class, 'openSourceProject'])->name('open-source.show');
Route::get('/reports', [PageController::class, 'reports'])->name('reports');
Route::get('/cv', [PageController::class, 'cv'])->name('cv');
Route::get('/cv.pdf', [PageController::class, 'cvFile'])->name('cv.file');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
