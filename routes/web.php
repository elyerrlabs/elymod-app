<?php

use Illuminate\Support\Facades\Route;

Route::middleware(["throttle:third-party:elymod-app:web"])->group(function () {

    Route::group([
        'prefix' => 'users',
        'as' => 'users.'
    ], function () {

        Route::get(
            '/',
            [\ElymodApp\App\Http\Controllers\UserController::class, 'index']
        )->name('index');
    });
});