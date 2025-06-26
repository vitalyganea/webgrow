<?php

use App\Http\Controllers\Public\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/{langCode?}', HomeController::class)->name('home');
require __DIR__ . '/auth.php';
