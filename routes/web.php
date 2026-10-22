<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\GameListController;
use App\Http\Controllers\GameGroupController;
use App\Http\Controllers\GameUserController;
use App\Services\IGDBService;
use App\Models\GameGroup;

Route::view('/', 'home')->name('login')->middleware('guest');
Route::post('/',[UserController::class, 'store'])->name('index.store')->middleware(['guest','throttle:2,1']);

Route::view('/codeaccess', 'codeAccess')->name('codeaccess')->middleware('guest');
Route::post('/codeaccess', [SessionController::class, 'store'])->name('codeaccess.store')->middleware(['guest','throttle:5,1']);

Route::get('/logout', [SessionController::class, 'destroy'])->name('logout')->middleware('auth');

Route::get('/games', [GameListController::class, 'index'])->name('games.list')->middleware('auth');
Route::get('/games/new/{group}', [GameListController::class,'create'])->name('games.new')->middleware('auth');
Route::post('/games/new/{group}', function(Request $request, GameGroup $group){
    $igdb = new IGDBService();
    $games = $igdb->search($request);
    return view( 'games.create', [
                    'games' => $games,
                    'group' => $group,
                    ] );
} )->name('IGDB.search')->middleware('auth');


Route::post('/games/new', [GameUserController::class, 'store'])->name('games.store')->middleware('auth');
Route::post('/games/toggle-finished', [GameUserController::class, 'toggleFinished'])->name('games.toggle.finished')->middleware('auth');
Route::delete('/games/delete/{gameUser}', [GameUserController::class, 'destroy'])->name('games.delete')->middleware('auth');

Route::get('/groups/new', [GameGroupController::class, 'create'])->name('groups.create')->middleware('auth');
Route::post('/groups/new', [GameGroupController::class, 'store'])->name('groups.store')->middleware('auth');

Route::post('/games/finished', function(){
    if( session('show_finished') ){
        session(['show_finished'=>false]) ;
    }else{
        session(['show_finished'=>true]);
    }
    return back();
})->name('toggle.finished');

Route::post('/profile/theme', function(){
    
        
    if( Illuminate\Support\Facades\Auth::user()->theme =='dark' ){
        Illuminate\Support\Facades\Auth::user()->update(['theme'=>'light']);
    }else{
        Illuminate\Support\Facades\Auth::user()->update(['theme' => 'dark']);
    }
    return back();
})->name('toggle.theme');