<?php

declare(strict_types=1);

use App\Http\Controllers\Customer\Auth\AuthenticatedAccountController;
use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\Customer\Auth\LogoutController;
use App\Http\Controllers\Customer\Auth\RefreshTokenController;
use App\Http\Controllers\Customer\Auth\RegisterController;
use App\Http\Controllers\Customer\Auth\ResendEmailVerificationController;
use App\Http\Controllers\Customer\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::name('customer.')
    ->group(function (): void {
        Route::prefix('auth')
            ->name('auth.')
            ->group(function (): void {
                Route::post('register', RegisterController::class)
                    ->middleware('throttle:10,1')
                    ->name('register');

                Route::post('login', LoginController::class)->name('login');

                Route::post('refresh', RefreshTokenController::class)
                    ->middleware('throttle:30,1')
                    ->name('refresh');

                Route::middleware('auth:api')->group(function (): void {
                    Route::post('logout', LogoutController::class)->name('logout');

                    Route::middleware('verified')->group(function (): void {
                        Route::get('user', AuthenticatedAccountController::class)->name('user');
                    });
                });
            });
    });

Route::post('auth/email/verification-notification', ResendEmailVerificationController::class)
    ->middleware('throttle:6,1')
    ->name('verification.send');

Route::get('email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
