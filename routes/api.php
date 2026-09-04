<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\ResendEmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('email/verification-notification', ResendEmailVerificationController::class)->middleware(
        'throttle:6,1'
    )->name('verification.send');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
});

Route::get('email/verify/{id}/{hash}', EmailVerificationController::class)
    ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::middleware('verified')->group(function () {
            Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
            Route::get('user', [AuthController::class, 'user'])->name('auth.user');
        });
    });
});
