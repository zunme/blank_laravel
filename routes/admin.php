<?php

use App\Http\Controllers\Admin\Setting\BoardController;
use Illuminate\Support\Facades\Route;

Route::group([
    'as' => 'adm.',
    'middleware' => ['level:admin'],
], function () {
    Route::get('refresh', function () {
        session()->regenerate();
        return response()->json([
            "token"=>csrf_token()],
        200);
    });
    Route::get('home', [App\Http\Controllers\Admin\HomeController::class, 'index'])->name('home');

    Route::get('user', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('user');
    Route::get('user/list', [App\Http\Controllers\Admin\UserController::class, 'list']);
    Route::post('user/save', [App\Http\Controllers\Admin\UserController::class, 'save']);

    Route::get('rooms', [App\Http\Controllers\Admin\RoomController::class, 'index'])->name('rooms');
    Route::get('rooms/list', [App\Http\Controllers\Admin\RoomController::class, 'list']);
    Route::post('rooms/save', [App\Http\Controllers\Admin\RoomController::class, 'save']);

    Route::get('test', [App\Http\Controllers\Admin\RoomController::class, 'test']);

    Route::group([
        'prefix' => 'setting',
    ], function () {
        Route::resource('board-infos', BoardController::class);
    });
});