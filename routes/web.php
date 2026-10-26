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
    $games = $igdb->search2($request);
    return view( 'games.create', [
                    'games' => $games,
                    'group' => $group,
                    ] );
} )->name('IGDB.search')->middleware('auth');

//GRUPOS
/*Route::get('/groups/new', [GameGroupController::class, 'create'])->name('groups.create')->middleware('auth');*/
Route::post('/groups/new', [GameGroupController::class, 'store'])->name('groups.store')->middleware('auth');
Route::patch('/groups/{gameGroup}', [GameGroupController::class, 'update'])->name('groups.update')->middleware('auth');
Route::delete('/groups/{gameGroup}', [GameGroupController::class, 'destroy'])->name('groups.delete')->middleware('auth');

//JUEGOS
Route::post('/games/new', [GameUserController::class, 'store'])->name('games.store')->middleware('auth');
Route::post('/games/toggle-finished', [GameUserController::class, 'toggleFinished'])->name('games.toggle.finished')->middleware('auth');
Route::delete('/games/delete/{gameUser}', [GameUserController::class, 'destroy'])->name('games.delete')->middleware('auth');

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

Route::get('/api/games/search', function(Request $request){
    $request->validate([
        'q' => ['required', 'string', 'min:4']
    ]);

    $igdb = new IGDBService();
    $games = $igdb->search($request->q);
    return response()->json($games);
})->name('games.search');

Route::get('/api/icons/search', function(Request $request, BladeUI\Icons\Factory $factory){

$search = strtolower($request->get('q', ''));
       $search = strtolower($request->get('q', ''));
        $allIcons = [];

        // Iteramos sobre el array de configuraciones de los sets instalados
        foreach ($factory->all() as $set) {
            $prefix = $set['prefix'] ?? '';
            $paths = $set['paths'] ?? [];

            if (empty($prefix) || empty($paths)) {
                continue;
            }

            // Recorremos cada directorio físico asignado a este set de iconos
            foreach ($paths as $path) {
                if (!File::isDirectory($path)) {
                    continue;
                }

                // Obtenemos todos los archivos .svg del directorio
                $files = File::files($path);

                foreach ($files as $file) {
                    if ($file->getExtension() === 'svg') {
                        // Nombre del archivo sin la extensión .svg
                        $iconName = $file->getFilenameWithoutExtension();
                        
                        // Formamos el nombre completo: "prefix-nombre" (ej: heroicon-o-user)
                        $fullName = $prefix . '-' . $iconName;

                        // Filtramos si hay una búsqueda activa
                        if (empty($search) || str_contains($fullName, $search)) {
                            $allIcons[] = $fullName;
                        }
                    }
                }
            }
        }

        // Ordenamos alfabéticamente y eliminamos posibles duplicados
        $allIcons = array_unique($allIcons);
        sort($allIcons);
        // Limitamos el resultado (ej: 50 elementos) para mantener el rendimiento del buscador
        return response()->json(array_slice($allIcons, 0, 42));

})->name('icons.search');

Route::get('/icons/svg', function(Request $request, BladeUI\Icons\Factory $factory){
$name = $request->get('name');
    try {
        // SVG directo listo para inyectar con x-html
        return response($factory->svg($name)->toHtml(), 200)
                ->header('Content-Type', 'image/svg+xml');
    } catch (\Exception $e) {
        return response('', 404);
    }
})->name('icons.svg');