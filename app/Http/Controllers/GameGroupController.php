<?php

namespace App\Http\Controllers;

use App\Models\GameGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class GameGroupController extends Controller
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
        return view('groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string', 
                'min:5', 
                'max:50', 
                Rule::unique('game_groups')->where( fn(Builder $query) => $query->where('user_id', Auth::id() ) )],
            'color' => ['required', 'string', 'hex_color'],
            'icon' => ['required', 'string', 'starts_with:heroicon,mdi'],
        ]);

        $userLastGroup = GameGroup::where('user_id', Auth::id())->orderBy('order','DESC')->first();
        $order = (( ! empty($userLastGroup) )? $userLastGroup->order + 1 : 1 );

        GameGroup::create([
            'user_id' => Auth::id(),
            'groupname' => $request->name,
            'color' => $request->color,
            'icon' => $request->icon,
            'order' => $order,
        ]);

        return redirect()->route( 'games.list' )->with('success','El grupo se ha creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(GameGroup $gameGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GameGroup $gameGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GameGroup $gameGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameGroup $gameGroup)
    {
        if($gameGroup->user_id != Auth::id()){
            return route_to('games.list');
        }

        $gameGroup->delete();
        return route_to('game.list')->with('success', 'Se ha eliminado el grupo y su contenido');
    }
}
