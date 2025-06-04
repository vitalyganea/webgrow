<?php

use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\SeriesController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('series', SeriesController::class)->name('series');

Route::get('articles', ArticleController::class)->name('articles');

require __DIR__ . '/auth.php';
