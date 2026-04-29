<?php

use App\Http\Controllers\AdminAuthController;
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

    Route::get('/admin', [AdminShellController::class, 'index'])->name('admin.index');
    Route::get('/admin/dashboard', [AdminShellController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/{any?}', [AdminShellController::class, 'index'])
        ->where('any', '.*')
        ->name('admin.spa');
});
