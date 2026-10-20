<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Models\GameGroup;
use App\Models\GameUser;
use App\Models\Game;
use App\Models\Platform;
use App\Models\Developer;
use App\Services\IGDBService;
use App\Actions\SaveUserGame;

class GameListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Auth::user()->gameGroups()->latest()->get();
        $userGames = Auth::user()->userGames()->get();

        return view('games.list', [
            'groups' => $groups,
            'userGames' => $userGames,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(GameGroup $group)
    {
        //hacer esto con politica
        if ($group->user_id != Auth::id()){
            return back()->with('error', 'No tienes permiso para hacer esto');;
        }

        return view('games.create', [
            'group' => $group
        ]);
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
            return to_route('games.list');
        else
            return to_route('games.list')->with('error', 'Ocurrió un problema');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
