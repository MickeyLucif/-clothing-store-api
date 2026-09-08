<?php

declare(strict_types=1);

use App\Enum\Permission as PermissionEnum;
use App\Http\Controllers\Admin\Auth\AuthenticatedAccountController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Http\Controllers\Admin\Auth\RefreshTokenController;
use App\Http\Controllers\Admin\Roles\IndexRoleController;
use App\Http\Controllers\Admin\Roles\StoreRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::prefix('auth')
            ->name('auth.')
            ->group(function (): void {
                Route::post('login', LoginController::class)->name('login');

                Route::post('refresh', RefreshTokenController::class)
                    ->middleware('throttle:30,1')
                    ->name('refresh');

                Route::middleware('auth:admin')->group(function (): void {
                    Route::post('logout', LogoutController::class)->name('logout');
                    Route::get('admin', AuthenticatedAccountController::class)->name('admin');
                });
            });

        Route::middleware('auth:admin')->group(function (): void {
            Route::prefix('roles')
                ->name('roles.')
                ->group(function (): void {
                    Route::get('/', IndexRoleController::class)
                        ->middleware('can:'.PermissionEnum::VIEW_ROLES->value)
                        ->name('index');

                    Route::post('/', StoreRoleController::class)
                        ->middleware('can:'.PermissionEnum::CREATE_ROLES->value)
                        ->name('store');
                });
        });
    });
