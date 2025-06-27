<?php

use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/{langCode?}', HomeController::class)
    ->where('langCode', '[a-z]{2}')
    ->name('home');

require __DIR__ . '/auth.php';
