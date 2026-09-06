<?php

declare(strict_types=1);

use App\Http\Controllers\Web\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::view('/', 'dashboard')->name('dashboard');


        Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
        });
    });
