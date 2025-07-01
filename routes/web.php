<?php

use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\FormController;
Route::get('/{langCode?}', HomeController::class)
    ->where('langCode', '[a-z]{2}')
    ->name('home');

Route::post('/post-form', [FormController::class, 'store']);

require __DIR__ . '/auth.php';
