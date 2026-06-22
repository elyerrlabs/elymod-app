<?php

use Illuminate\Support\Facades\Route;

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
        Route::get(
            '/',
            [\ElymodApp\App\Http\Controllers\SettingController::class, 'general']
        )->name('general');
    });
});
