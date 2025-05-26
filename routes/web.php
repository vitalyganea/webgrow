<?php

use App\Http\Controllers\Admin\Auth\AccountController;
use App\Http\Controllers\Admin\Auth\DangerController;
use App\Http\Controllers\Admin\Auth\SecurityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeriesController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('series', SeriesController::class)->name('series');

Route::get('articles', ArticleController::class)->name('articles');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('settings/account', [AccountController::class, 'index'])->name('settings.account');
    Route::patch('settings/account', [AccountController::class, 'update']);

    Route::get('settings/security', [SecurityController::class, 'index'])->name('settings.security');
    Route::put('settings/security', [SecurityController::class, 'update']);

    Route::get('settings/danger', [DangerController::class, 'index'])->name('settings.danger');
    Route::delete('settings/danger', [DangerController::class, 'delete']);

    // List all pages
    Route::get('pages', [PageController::class, 'index'])->name('get.pages');

    // Show create form
    Route::get('pages/create', [PageController::class, 'create'])->name('create.page');

    // Store new page
    Route::post('pages', [PageController::class, 'store'])->name('store.page');

    // Show edit form
    Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('edit.page');

    // Update page
    Route::put('pages/{page}', [PageController::class, 'update'])->name('update.page');

    // Delete page
    Route::delete('pages/{page}', [PageController::class, 'destroy'])->name('delete.page');});

require __DIR__ . '/auth.php';
