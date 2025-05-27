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

require __DIR__ . '/auth.php';
