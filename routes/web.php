<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/t', function () {
    $data = ['data'=>[]];
    event(new \App\Events\SendMessage($data));
    dd('Event Run Successfully.');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group([
    'middleware' => ['level:user'],
], function () {
    Route::get('/room/prc', [App\Http\Controllers\Front\RoomController::class, 'roomPrc']);
    Route::get('/room/entrance', [App\Http\Controllers\Front\RoomController::class, 'entrance']);
    Route::get('/room/exit', [App\Http\Controllers\Front\RoomController::class, 'xit']);
});