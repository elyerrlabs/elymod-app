<?php

use Illuminate\Support\Facades\Route;
use ElymodApp\App\Http\Controllers\SettingsController;

/**
 * Register admin routes
 */

Route::middleware("throttle:third-party:elymod-app:admin")->group(function () {

    Route::get('/admin', [
        \ElymodApp\App\Http\Controllers\AdminController::class,
        'index'
    ])->name('admin.index');



    Route::group([
        'prefix' => 'settings',
        'as' => 'settings.'
    ], function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/update', [SettingsController::class, 'update'])->name('update');
    });
});
