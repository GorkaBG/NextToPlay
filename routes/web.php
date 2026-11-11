<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\GameGroupController;
use App\Http\Controllers\GameUserController;
use Illuminate\Support\Facades\Storage;

Route::get('/debug-storage', function () {
    $disk = Storage::disk('wasmer_persistent');
    $path = 'covers/test.txt';
    $disk->put($path, 'prueba de persistencia');
    
    
    return [
        'path_absoluta' => $disk->path($path),
        'existe' => $disk->exists($path),
        'url' => $disk->url($path),
        'env_actual' => env('APP_ENV')
    ];
});

/**
 * Rutas de acceso para el login, en las que se solicita el email y el código. Para prevenir abusos, se permiten 3 solicitudes de código y 5 intentos de verificación por minuto antes de mostrar el error de peticiones excedidas.
 */
Route::middleware('guest')->group(function(){  

    Route::view('/', 'home')->name('login');
    Route::post('/',[UserController::class, 'store'])->name('index.store')->middleware('throttle:3,1');
    
    Route::view('/codeaccess', 'codeAccess')->name('codeaccess');
    Route::post('/codeaccess', [SessionController::class, 'store'])->name('codeaccess.store')->middleware('throttle:5,1');

});

/**
 * Rutas privadas para los usuarios conectados con las funcionalidades de la aplicación
 */
Route::middleware('auth')->group(function(){    

    Route::delete('/logout', [SessionController::class, 'destroy'])->name('logout');

    /**
     * Rutas del listado de juegos
     */
    Route::get('/games', [GameUserController::class, 'index'])->name('games.list');
    Route::post('/games/finished', [GameUserController::class, 'toggleShowFinished'])->name('games.show.finished');
    Route::post('/games/reorder', [GameUserController::class, 'changePosition'])->name('games.reorder');

    /**
     * Acciones de juegos del usuario
     */
    Route::post('/games/new', [GameUserController::class, 'store'])->name('games.store');
    Route::patch('/games/{gameUser}/toggle-finished', [GameUserController::class, 'toggleFinished'])->name('games.toggle.finished');
    Route::patch('/games/{gameUser}/update', [GameUserController::class, 'update'])->name('games.update');
    Route::delete('/games/{gameUser}/delete', [GameUserController::class, 'destroy'])->name('games.delete');

    /**
     * Acciones Ajax del modal de juegos
     */
    Route::get('/api/games/search', [GameUserController::class, 'igdbSearch'])->name('games.search');
    Route::get('/api/games/game_platforms', [GameUserController::class, 'gamePlatforms'])->name('games.game_platform');

    /**
     * Acciones de grupos del usuario
     */
    Route::post('/groups/new', [GameGroupController::class, 'store'])->name('groups.store');
    Route::patch('/groups/{gameGroup}', [GameGroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{gameGroup}', [GameGroupController::class, 'destroy'])->name('groups.delete');

    /**
     * Acciones del modal de grupos
     */
    Route::get('/api/icons/search', [GameGroupController::class,'iconSearch'])->name('icons.search');
    Route::get('/icons/svg', [GameGroupController::class, 'getIcon'])->name('icons.svg');

    /**
     * Acciones de usuario
     */
    Route::post('/user/theme', [UserController::class, 'toggleTheme'])->name('toggle.theme');

    /** Rutas para wasmer, donde no funciona el enlace simbólico */
    Route::get('/media/{path}', function (string $path) {
        if (!Storage::disk('wasmer_persistent')->exists($path)) {
            abort(404);
        }
        return Storage::disk('wasmer_persistent')->response($path);
    })->where('path', '.*')->name('media.show');
});
