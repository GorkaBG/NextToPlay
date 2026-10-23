<?php

namespace App\Http\Controllers;

use App\Models\GameUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'platforms' => ['required', 'array'],
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
        //
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
}
