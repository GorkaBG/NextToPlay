<?php

namespace App\Http\Controllers;

use App\Models\GameUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\GameGroup;
use App\Services\IGDBService;
use App\Actions\SaveUserGame;

class GameUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //hacer esto con politica        
        if ( GameGroup::find($request->group)->user_id != Auth::id()){
            return back()->with('error', 'No tienes permiso para hacer esto');;
        }

        $request->validate([
            'game' => ['required', 'integer', 'max_digits:6'],
            'group' => ['required', 'integer', 'max_digits:2'],
            'platforms' => ['array'],
        ]);
    
        if( (new SaveUserGame)->handle($request) ) 
            return to_route('games.list')->with('success', 'Juego añadido al listado');
        else
            return to_route('games.list')->with('error', 'Ocurrió un problema');
    }

    /**
     * Display the specified resource.
     */
    public function show(GameUser $gameUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GameUser $gameUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GameUser $gameUser)
    {
        //hacer esto con politica        
        if ( $gameUser->user_id != Auth::id()){
            return back()->with('error', 'No tienes permiso para hacer esto');;
        }

        $request->validate([
            'name' => ['string', 'min:5','max:255'],
            'finished' => ['sometimes', 'accepted'],
            'platforms' => ['array'],
        ]);
        
        $gameUser->update([
            'custom_title' => $request->name,
            'finished' => (($request->finished == 'on')? true : false),
        ]);
        $gameUser->platforms()->detach();
        $gameUser->platforms()->attach($request->platforms);

        return to_route('games.list')->with('success','Juego editado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameUser $gameUser)
    {      
        //hacer esto con politica  
        if($gameUser->user_id != Auth::id()){
            return to_route('games.list')->with('error', 'Ocurrió un error');        
        }

        $gameUser->delete();
        return to_route('games.list')->with('success', 'Juego eliminado correctamente');        
    }

    public function toggleFinished(Request $request)
    {
        $request->validate([
            'gameId' => ['integer','min-digits:1', 'max-digits:2'],
        ]);

        $gameUser = GameUser::find( $request->gameId );

        if($gameUser->user_id != Auth::id()){
            return to_route('games.list');
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
     * Modifica el orden de todos los juegos de un grupo a partir del juego indicado y la posición final. Para ello se obtienen todos los ítems del grupo y se les asigna su nueva posición (en base al índica $k, al empezar en 0 la posición hasta el objeto que se ordena aumenta en uno, caundo éste es encontrado se pasa a aumentarlo en 2, loq ue hace que quede esa posición del orden libre para asignárla). Se tienen en cuenta los elementos completados aunque no estén visibles (y por lo tanto no entren en el conteo del tablero) o estén colocados al final independientemente de su orden. De esta forma en vez de pasar a ser los últimos, mantienen su orden original alterado solo por las nuevas incorporaciones por encima suyo.
     * 
     */
    public function changePosition(Request $request)
    {
        //hacer esto con politica        
        if ( GameGroup::find($request->group_id)->user_id != Auth::id()){
            return back()->with('error', 'No tienes permiso para hacer esto');
        }

        $request->validate([
            'id' => ['required','integer','max_digits:8'],
            'group_id' => ['required','integer','max_digits:8'],
            'position' => ['required','integer','max_digits:3'],            
        ]);

        $game = GameUser::find($request->id);

        $groupGames = GameUser::where('game_group_id', $request->group_id)->where('id','!=',$request->id)->orderBy('position','Asc')->get();

        DB::transaction(function () use($request, $groupGames, $game) {
            $finished=0;
            $found=false; 
            if(!empty($groupGames)){
                foreach($groupGames as $k=>$gameAux){               
                    if($k+1 == $request->position + $finished)
                        $found=true;

                    $gameAux->update([
                        'position' => (( ($k+1 < $request->position + $finished) && ! $found)? $k+1 : $k+2),
                    ]); 
                    if($gameAux->finished && !$found)
                        $finished++; 
                }   
            }

            $game->update([
                'game_group_id' => $request->group_id,
                'position' => $request->position+$finished,
            ]);   
        });

    }
}
