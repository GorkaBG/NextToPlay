<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;

Route::view('/', 'home')->name('login')->middleware('guest');
Route::post('/',[UserController::class, 'store'])->name('index.store')->middleware(['guest','throttle:4,1']);
Route::view('/codeaccess', 'codeAccess')->name('codeaccess')->middleware('guest');
Route::post('/codeaccess', [SessionController::class, 'store'])->name('codeaccess.store')->middleware(['guest','throttle4:1']);

/*Route::get('/games', [GameController::class, 'index'])->name('games.list')->middleware('auth');*/
Route::view('/games', 'games.list')->name('games.list')->middleware('auth');;//TEMPORAL