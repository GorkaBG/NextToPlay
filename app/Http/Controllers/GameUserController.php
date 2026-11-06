<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Models\Game;
use App\Models\GameGroup;
use App\Models\GameUser;
use App\Services\IGDBService;
use App\Actions\SaveUserGame;
use App\Actions\ChangeUserGameOrder;

class GameUserController extends Controller
{
    /**
     * Muestra el listado de juegos del usuario conectado en los grupos generados y ordenados según su criterio
     */
    public function index(): View
    {
        $groups = Auth::user()->gameGroups()->orderBy('order','Asc')->get();
        $userGames = Auth::user()->userGames()->orderBy('finished','Asc')->orderBy('position','Asc')->get();

        return view('games.list', [
            'groups' => $groups,
            'userGames' => $userGames,
        ]);
    }

    /**
     * Asigna un juego al usuario conectado. Si el juego seleccioando no existe aún en la base de datos, también guardará sus datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'game' => ['required', 'integer', 'max_digits:6'],
            'group' => ['required', 'integer', 'max_digits:2'],
            'platforms' => ['array'],
        ]);

        if( Gate::denies('workWith', GameGroup::find($request->group)) ){
            return back()->with('error', 'No tienes permiso para hacer esto');
        } 
    
        if( (new SaveUserGame)->handle($request) ) 
            return to_route('games.list')->with('success', 'Juego añadido al listado');
        else
            return to_route('games.list')->with('error', 'Ocurrió un problema');
    }

    
    /**
     * Actualiza los datos de un juego del usuario conectado, pudiendo asignar un nombre personalizado, cambiar su estado y las plataormas en las que lo posee
     */
    public function update(Request $request, GameUser $gameUser): RedirectResponse
    {
        $request->validate([
            'name' => ['string', 'min:5','max:255'],
            'finished' => ['sometimes', 'accepted'],
            'platforms' => ['array'],
        ]);
        
        if( Gate::denies('workWith', $gameUser) ){
            return back()->with('error', 'No tienes permiso para hacer esto');
        }

        $gameUser->update([
            'custom_title' => $request->name,
            'finished' => $request->boolean('finished'),
        ]);
        $gameUser->platforms()->sync($request->platforms);

        return to_route('games.list')->with('success','Juego editado correctamente');
    }

    /**
     * Elimina la asignación de un juego al usuario conectado.
     */
    public function destroy(GameUser $gameUser): RedirectResponse
    {      
        if( Gate::denies('workWith', $gameUser) ){
            return back()->with('error', 'No tienes permiso para hacer esto');
        }

        $gameUser->delete();
        return to_route('games.list')->with('success', 'Juego eliminado correctamente');        
    }

    /**
     * Cambia el estado del juego para el usuario
     */
    public function toggleFinished(GameUser $gameUser): RedirectResponse
    {
        //hacer esto con politica  
        if($gameUser->user_id != Auth::id()){
            return to_route('games.list')->with('error', 'Ocurrió un error');        
        }

        $gameUser->update([
            'finished' => ! $gameUser->finished,
        ]);

        if($gameUser->finished === true)
            return to_route('games.list')->with('success', '¡Enhorabuena! Ahora... ¡A por el siguiente!');
        else    
            return to_route('games.list');
        
    }

    /**
     * Modifica el orden de todos los juegos de un grupo a partir del juego indicado y la posición final.  
     */
    public function changePosition(Request $request)
    {

        $request->validate([
            'id' => ['required','integer','max_digits:8'],
            'group_id' => ['required','integer','max_digits:8'],
            'position' => ['required','integer','max_digits:3'],            
        ]);

        //hacer esto con politica                
        if( Gate::denies('workWith', GameGroup::find($request->group_id)) ){
            return back()->with('error', 'No tienes permiso para hacer esto');
        } 

        $game = GameUser::find($request->id);

        $groupGames = GameUser::where('game_group_id', $request->group_id)->where('id','!=',$request->id)->orderBy('position','Asc')->get();

        (new ChangeUserGameOrder)->changeOrder($request, $groupGames, $game);

    }

    /**
     * Cambia el estado de visibilidad de losjuegos completados para el usuario. Solo dura durante la sesión.
     */
    public function toggleShowFinished(): RedirectResponse
    {
        session(['show_finished'=> ! session('show_finished', false) ]);
        return back();
    }

    /**
     * Devuelve el resultado de una búsqueda en IGDB según el texto que se le pase
     */
    public function igdbSearch(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:4']
        ]);

        $games = (new IGDBService)->search($request->q);
        return response()->json($games);
    }

    /**
     * Devuelve el listado de plataformas en las que un juego está disponible
     */
    public function gamePlatforms(Request $request): JsonResponse
    {        
        $request->validate([
            'q' => ['required', 'integer', 'max_digits:7']
        ]);
        $game = Game::findOrFail($request->q);
        return response()->json($game->platforms);
    }
}
