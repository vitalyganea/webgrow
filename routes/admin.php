<?php

use App\Http\Controllers\Admin\LanguageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AccountController;
use App\Http\Controllers\Admin\Auth\DangerController;
use App\Http\Controllers\Admin\Auth\SecurityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;

Route::prefix('admin')->name('admin.')->group(function () {

    // Guest routes (e.g., login, register)
    Route::middleware('guest')->group(function () {
        Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('register', [RegisteredUserController::class, 'store']);

        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    });

    // Authenticated routes (e.g., logout, email verification)
    Route::middleware('auth')->group(function () {
        Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        // Admin dashboard and settings routes
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('settings/account', [AccountController::class, 'index'])->name('settings.account');
        Route::patch('settings/account', [AccountController::class, 'update']);

        Route::get('settings/security', [SecurityController::class, 'index'])->name('settings.security');
        Route::put('settings/security', [SecurityController::class, 'update']);

        Route::get('settings/danger', [DangerController::class, 'index'])->name('settings.danger');
        Route::delete('settings/danger', [DangerController::class, 'delete']);

        Route::get('pages', [PageController::class, 'index'])->name('get.pages');
        Route::get('pages/create', [PageController::class, 'create'])->name('create.page');
        Route::post('pages', [PageController::class, 'store'])->name('store.page');

        // Use group_id for edit, update, delete
        Route::get('pages/{group_id}/edit', [PageController::class, 'edit'])->name('edit.page');
        Route::put('pages/{group_id}', [PageController::class, 'update'])->name('update.page');
        Route::delete('pages/{group_id}', [PageController::class, 'destroy'])->name('delete.page');


        Route::get('page/edit-html', [PageController::class, 'editHtml'])->name('admin.editHtml');
        Route::post('page/update-html', [PageController::class, 'updateHtml'])->name('admin.updateHtml');


        Route::get('languages', [LanguageController::class, 'index'])->name('get.languages');
        Route::get('languages/create', [LanguageController::class, 'create'])->name('create.language');
        Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');
        Route::get('languages/{language}/edit', [LanguageController::class, 'edit'])->name('edit.language');
        Route::put('languages/{language}', [LanguageController::class, 'update'])->name('update.language');
        Route::delete('languages/{language}', [LanguageController::class, 'destroy'])->name('delete.language');


    });
});
