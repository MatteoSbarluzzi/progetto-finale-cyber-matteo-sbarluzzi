<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\WriterController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\RevisorController;
use App\Http\Controllers\ProfileController;

// Public routes
Route::get('/', [PublicController::class, 'homepage'])->name('homepage');

Route::get('/careers', [PublicController::class, 'careers'])->name('careers');
Route::post('/careers/submit', [PublicController::class, 'careersSubmit'])->name('careers.submit');

// Articoli pubblici
Route::get('/articles/index', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/show/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/articles/category/{category}', [ArticleController::class, 'byCategory'])->name('articles.byCategory');
Route::get('/articles/user/{user}', [ArticleController::class, 'byUser'])->name('articles.byUser');

// Ricerca (rate limited via AppServiceProvider)
Route::get('/articles/search', [ArticleController::class, 'articleSearch'])
    ->name('articles.search')
    ->middleware('throttle:articles-search');

// Authenticated routes (Policies decidono i permessi)
Route::middleware('auth')->group(function () {
// CRUD articoli protetti da policy (create/update/delete, ecc.)
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles/store', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/edit/{article}', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/update/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/destroy/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

// Writer dashboard (policy: create su Article)
    Route::get('/writer/dashboard', [WriterController::class, 'dashboard'])->name('writer.dashboard');

// Revisor (policy: review/publish su Article)
    Route::get('/revisor/dashboard', [RevisorController::class, 'dashboard'])->name('revisor.dashboard');
    Route::post('/revisor/{article}/accept', [RevisorController::class, 'acceptArticle'])->name('revisor.acceptArticle');
    Route::post('/revisor/{article}/reject', [RevisorController::class, 'rejectArticle'])->name('revisor.rejectArticle');
    Route::post('/revisor/{article}/undo',   [RevisorController::class, 'undoArticle'])->name('revisor.undoArticle');

// Profile (challenge 6): vulnerabile ma vincolato alla propria utenza via policy
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'updateVulnerable'])->name('profile.update');
});

// Clickjacking test (per le tue challenge)
Route::view('/attaccante', 'Clickjacking test.attaccante')->name('attaccante');
Route::view('/vittima', 'Clickjacking test.vittima')->name('vittima');

// Admin routes
Route::middleware(['auth', 'admin.local'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// Ruoli
    Route::patch('/admin/{user}/set-admin',   [AdminController::class, 'setAdmin'])->name('admin.setAdmin');
    Route::patch('/admin/{user}/set-revisor', [AdminController::class, 'setRevisor'])->name('admin.setRevisor');
    Route::patch('/admin/{user}/set-writer',  [AdminController::class, 'setWriter'])->name('admin.setWriter');

// Tags
    Route::put('/admin/edit/tag/{tag}',    [AdminController::class, 'editTag'])->name('admin.editTag');
    Route::delete('/admin/delete/tag/{tag}', [AdminController::class, 'deleteTag'])->name('admin.deleteTag');
    Route::post('/admin/tag/store',        [AdminController::class, 'storeTag'])->name('admin.storeTag');

// Categories
    Route::put('/admin/edit/category/{category}',    [AdminController::class, 'editCategory'])->name('admin.editCategory');
    Route::delete('/admin/delete/category/{category}', [AdminController::class, 'deleteCategory'])->name('admin.deleteCategory');
    Route::post('/admin/category/store',             [AdminController::class, 'storeCategory'])->name('admin.storeCategory');
});


