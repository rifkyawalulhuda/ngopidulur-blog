<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminApi\CategoryController as AdminCategoryApiController;
use App\Http\Controllers\AdminApi\DashboardController as AdminDashboardApiController;
use App\Http\Controllers\AdminApi\TagController as AdminTagApiController;
use App\Http\Controllers\AdminShellController;
use App\Http\Controllers\PublicHomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicHomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/admin/api/login', [AdminAuthController::class, 'store'])->name('admin.api.login');
});

Route::middleware('auth')->group(function () {
    Route::post('/admin/api/logout', [AdminAuthController::class, 'destroy'])->name('admin.api.logout');

    Route::prefix('admin/api')->group(function () {
        Route::get('/dashboard', [AdminDashboardApiController::class, 'index'])->name('admin.api.dashboard');

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
    });

    Route::get('/admin', [AdminShellController::class, 'index'])->name('admin.index');
    Route::get('/admin/dashboard', [AdminShellController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/{any?}', [AdminShellController::class, 'index'])
        ->where('any', '.*')
        ->name('admin.spa');
});
