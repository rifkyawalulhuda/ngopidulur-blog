<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminApi\PostController as AdminPostApiController;
use App\Http\Controllers\AdminApi\CategoryController as AdminCategoryApiController;
use App\Http\Controllers\AdminApi\DashboardController as AdminDashboardApiController;
use App\Http\Controllers\AdminApi\TagController as AdminTagApiController;
use App\Http\Controllers\AdminShellController;
use App\Http\Controllers\PublicCategoryController;
use App\Http\Controllers\PublicRobotsController;
use App\Http\Controllers\PublicSearchController;
use App\Http\Controllers\PublicPostController;
use App\Http\Controllers\PublicHomeController;
use App\Http\Controllers\PublicTagController;
use App\Http\Controllers\PublicSitemapController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::bind('post', function (string $value) {
    return Post::withTrashed()
        ->when(is_numeric($value), fn ($query) => $query->whereKey((int) $value), fn ($query) => $query->where('slug', $value))
        ->firstOrFail();
});

Route::get('/', [PublicHomeController::class, 'index'])->name('home');
Route::get('/posts/{slug}', [PublicPostController::class, 'show'])->name('posts.show');
Route::get('/category/{category}', [PublicCategoryController::class, 'show'])->name('category.show');
Route::get('/tag/{tag}', [PublicTagController::class, 'show'])->name('tag.show');
Route::get('/search', [PublicSearchController::class, 'index'])->name('search');
Route::get('/sitemap.xml', [PublicSitemapController::class, 'show'])->name('sitemap');
Route::get('/robots.txt', [PublicRobotsController::class, 'show'])->name('robots');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/admin/api/login', [AdminAuthController::class, 'store'])->name('admin.api.login');
});

Route::middleware('auth')->group(function () {
    Route::post('/admin/api/logout', [AdminAuthController::class, 'destroy'])->name('admin.api.logout');

    Route::prefix('admin/api')->group(function () {
        Route::get('/dashboard', [AdminDashboardApiController::class, 'index'])->name('admin.api.dashboard');

        Route::get('/posts', [AdminPostApiController::class, 'index'])->name('admin.api.posts.index');
        Route::post('/posts', [AdminPostApiController::class, 'store'])->name('admin.api.posts.store');
        Route::get('/posts/{post}', [AdminPostApiController::class, 'show'])->name('admin.api.posts.show');
        Route::put('/posts/{post}', [AdminPostApiController::class, 'update'])->name('admin.api.posts.update');
        Route::delete('/posts/{post}', [AdminPostApiController::class, 'destroy'])->name('admin.api.posts.destroy');
        Route::post('/posts/{post}/publish', [AdminPostApiController::class, 'publish'])->name('admin.api.posts.publish');
        Route::post('/posts/{post}/archive', [AdminPostApiController::class, 'archive'])->name('admin.api.posts.archive');
        Route::get('/posts/{post}/preview', [AdminPostApiController::class, 'preview'])->name('admin.api.posts.preview');

        Route::get('/categories', [AdminCategoryApiController::class, 'index'])->name('admin.api.categories.index');
        Route::post('/categories', [AdminCategoryApiController::class, 'store'])->name('admin.api.categories.store');
        Route::get('/categories/{category}', [AdminCategoryApiController::class, 'show'])->name('admin.api.categories.show');
        Route::put('/categories/{category}', [AdminCategoryApiController::class, 'update'])->name('admin.api.categories.update');
        Route::delete('/categories/{category}', [AdminCategoryApiController::class, 'destroy'])->name('admin.api.categories.destroy');

        Route::get('/tags', [AdminTagApiController::class, 'index'])->name('admin.api.tags.index');
        Route::post('/tags', [AdminTagApiController::class, 'store'])->name('admin.api.tags.store');
        Route::get('/tags/{tag}', [AdminTagApiController::class, 'show'])->name('admin.api.tags.show');
        Route::put('/tags/{tag}', [AdminTagApiController::class, 'update'])->name('admin.api.tags.update');
        Route::delete('/tags/{tag}', [AdminTagApiController::class, 'destroy'])->name('admin.api.tags.destroy');

        Route::get('/settings', [\App\Http\Controllers\AdminApi\SettingsController::class, 'show'])->name('admin.api.settings.show');
        Route::put('/settings', [\App\Http\Controllers\AdminApi\SettingsController::class, 'update'])->name('admin.api.settings.update');

        Route::get('/media', [\App\Http\Controllers\AdminApi\MediaController::class, 'index'])->name('admin.api.media.index');
    });

    Route::get('/admin', [AdminShellController::class, 'index'])->name('admin.index');
    Route::get('/admin/dashboard', [AdminShellController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/{any?}', [AdminShellController::class, 'index'])
        ->where('any', '.*')
        ->name('admin.spa');
});
