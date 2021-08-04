<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\Setting\BoardController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'adm.',
    'middleware' => ['level:admin'], // 이 부분이 미들웨어 파라미터("admin"이라는 값이 넘어감.)
], function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');

    Route::group([
        'prefix' => 'setting',
    ], function () {
        Route::resource('board-infos', BoardController::class);
    });
});